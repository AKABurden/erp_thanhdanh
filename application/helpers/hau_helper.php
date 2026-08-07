<?php
defined('BASEPATH') or exit('No direct script access allowed');
function ch_where($table = '', $text = '')
{
    // $_text = 'IF('.$table.'.'.$text.' = "products", "product",IF('.$table.'.'.$text.' = "materials", "nvl",IF('.$table.'.'.$text.' = "tools_supplies", "tools","items")))';
    $_text = 'IF(' . $table . '.' . $text . ' = "items", "items",IF(' . $table . '.' . $text . ' = "materials", "nvl",IF(' . $table . '.' . $text . ' = "tools_supplies", "tools","product")))';
    return $_text;
}
function type_bonus($type = 1)
{
    if ($type == 1) {
        $bonus = ['KTVT-01', 'KTVT-02', 'KTVT-03', 'KTVT-04', 'KTVT-05', 'KTVT-06', 'KTVT-07', 'KTVT-08'];
    } else {
        $bonus = ['KTVT-01', 'KTVT-02', 'KTVT-03', 'KTVT-04', 'KTVT-05'];
    }
    return $bonus;
}

function type_plan_propose()
{
    // $type_plan_propose = array(
    //     array(
    //         'id' => 'train',
    //         'name' => 'KẾ HOẠCH ĐÀO TẠO'
    //     ),
    //     array(
    //         'id' => 'repair',
    //         'name' => 'KẾ HOẠCH SỬA CHỮA'
    //     ),
    //     array(
    //         'id' => 'quality',
    //         'name' => 'KẾ HOẠCH KIỂM TRA CHẤT LƯỢNG SẢN PHẨM'
    //     ),
    //     array(
    //         'id' => 'performance',
    //         'name' => 'KẾ HOẠCH BẢO DƯỠNG ĐỊNH KỲ'
    //     ),
    //     array(
    //         'id' => 'calibration',
    //         'name' => 'KẾ HOẠCH BẢO HIỆU CHUẨN ĐỊNH KỲ'
    //     ),
    //     array(
    //         'id' => 'replace',
    //         'name' => 'KẾ HOẠCH VẬT TƯ THAY THẾ ĐỊNH KỲ'
    //     ),
    //     array(
    //         'id' => 'npl',
    //         'name' => 'KẾ HOẠCH MUA NPL'
    //     ),
    //     array(
    //         'id' => 'tools',
    //         'name' => 'KẾ HOẠCH MUA VĂN PHÒNG PHẨM'
    //     ),
    //     array(
    //         'id' => 'sanxuat',
    //         'name' => 'KẾ HOẠCH MUA VẬT TƯ SẢN XUẤT'
    //     ),
    //     array(
    //         'id' => 'vouchers_coupon',
    //         'name' => 'KẾ HOẠCH THU'
    //     ),
    //     array(
    //         'id' => 'pay_slip',
    //         'name' => 'KẾ HOẠCH CHI'
    //     ),
    //     array(
    //         'id' => 'purchases',
    //         'name' => 'KẾ HOẠCH MUA NGOÀI FC'
    //     ),
    //     array(
    //         'id' => 'kpi',
    //         'name' => 'KẾ HOẠCH ĐÁNH GIÁ'
    //     ),
    //     array(
    //         'id' => 'recruit',
    //         'name' => 'KẾ HOẠCH TUYỂN DỤNG'
    //     ),
    //     array(
    //         'id' => 'machining',
    //         'name' => 'KẾ HOẠCH GIA CÔNG'
    //     ),
    //     array(
    //         'id' => 'system',
    //         'name' => 'KẾ HOẠCH CẬP NHẬT HỆ THỐNG'
    //     ),
    //     array(
    //         'id' => 'reports',
    //         'name' => 'KẾ HOẠCH BÁO CÁO'
    //     ),
    //     array(
    //         'id' => 'lead_the_walk',
    //         'name' => 'KẾ HOẠCH XEM XÉT LÃNH ĐẠO'
    //     ),
    //     array(
    //         'id' => 'plan_approval',
    //         'name' => 'KẾ HOẠCH TRÌNH KÝ DUYỆT'
    //     ),
    //     array(
    //         'id' => 'inspection_plan',
    //         'name' => 'KẾ HOẠCH TRA SOÁT'
    //     ),
    //     array(
    //         'id' => 'departmental_plan',
    //         'name' => 'KẾ HOẠCH CÔNG VIỆC PHÒNG BAN'
    //     ),
    //     array(
    //         'id' => 'plan_items_offce',
    //         'name' => 'KẾ HOẠCH MUA VẬT TƯ THIẾT BỊ VĂN PHÒNG'
    //     ),
    //     array(
    //         'id' => 'issuance_plan',
    //         'name' => 'KẾ HOẠCH BAN HÀNH'
    //     )
    // );
    $type_plan_propose = get_table_where('tbl_type_plan_propose');
    return $type_plan_propose;
}
function UpdateLotCode($suppliers = '', $date = '')
{
    $code = get_table_where('tbl_code_lot', array('date' => $date, 'suppliers' => $suppliers), '', 'row');
    if (!empty($code)) {
        $CI = &get_instance();
        $ins = array();
        $ins['code'] = $code + 1;
        $CI->db->where('tbl_code_lot.id', $code->id);
        $CI->db->update('tbl_code_lot', $ins);
    } else {
        $CI = &get_instance();
        $ins = array();
        $ins['date'] = $date;
        $ins['suppliers'] = $suppliers;
        $ins['code'] = 1;
        $CI->db->insert('tbl_code_lot', $ins);
    }
}
function GetLotCode($suppliers = '', $date = '')
{
    if (empty($date)) {
        $date = date('Y-m-d');
    }
    $code = get_table_where('tbl_code_lot', array('date' => $date, 'suppliers' => $suppliers), '', 'row');
    if (!empty($code)) {
        return $code->code + 1;
    } else {
        $CI = &get_instance();
        $ins = array();
        $ins['date'] = $date;
        $ins['suppliers'] = $suppliers;
        $ins['code'] = 0;
        $CI->db->insert('tbl_code_lot', $ins);
        return 1;
    }
}
function last_day($thang = '', $year = NULL)
{
    $date = getdate();
    if ($year) {
        $year = $date['year'];
    }
    if ($thang == 1 || $thang == 3 || $thang == 5 || $thang == 7 || $thang == 8 || $thang == 10 || $thang == 12) {
        $ngaycuoi = 31;
    } elseif ($thang == 4 || $thang == 6 || $thang == 9 || $thang == 11) {
        $ngaycuoi = 30;
    } else {
        $ngaycuoi = 28;

        if ($year % 4 == 0) {
            if ($year % 100 == 0) {
                if ($year % 400 == 0) {
                    $ngaycuoi = 29;
                }
            }
        }
    }
    return $ngaycuoi;
}
function get_quantili_po($id = '')
{
    $count = 0;
    $items = get_table_where('tblpurchases_items', array('id' => $id), '', 'row');
    $main = get_table_where('tblpurchases', array('id' => $items->purchases_id), '', 'row');
    $CI = &get_instance();
    $CI->db->select('SUM(quantity) as quantitys');
    $CI->db->where('id_items', $items->product_id);
    $CI->db->where('type', $items->type);
    $CI->db->where('id_purchase_items', $items->id);
    $itemss = $CI->db->get('tblpurchase_to_order_items')->row();
    if (!empty($itemss)) {
        $count += $itemss->quantitys;
    }

    $CI = &get_instance();
    $CI->db->select('SUM(quantity_suppliers) as quantitys');
    $CI->db->where('tblpurchase_order_items.product_id', $items->product_id);
    $CI->db->where('tblpurchase_order_items.type', $items->type);
    $CI->db->where('tblpurchase_order.id_purchase_proce', $items->purchases_id);
    $CI->db->where('tblpurchase_order.check_purchase_all', 0);
    $CI->db->join('tblpurchase_order', 'tblpurchase_order.id=tblpurchase_order_items.id_purchase_order', 'left');
    $order_items = $CI->db->get('tblpurchase_order_items')->row();
    if (!empty($order_items)) {
        $count += $order_items->quantitys;
    }

    $CI = &get_instance();
    $CI->db->select('SUM(quantity_suppliers) as quantitys');
    $CI->db->where('tblpurchase_order_items.product_id', $items->product_id);
    $CI->db->where('tblpurchase_order_items.type', $items->type);
    $CI->db->where('tblpurchase_order.id_purchases', $items->purchases_id);
    $CI->db->where('tblpurchase_order.check_purchase_all', 0);
    $CI->db->join('tblpurchase_order', 'tblpurchase_order.id=tblpurchase_order_items.id_purchase_order', 'left');
    $id_purchases = $CI->db->get('tblpurchase_order_items')->row();
    if (!empty($id_purchases)) {
        $count += $id_purchases->quantitys;
    }
    return $count;
}
function format_status_number_order_tnh($id)
{
    $label = get_status_label_hau(6);
    $status_name = $id . ' ' . _l('tnh_order');
    $class = 'label label-' . $label;
    return $status_name;
}
function test_quantity_order($id)
{
    $dem = 0;
    $CI = &get_instance();
    $main = get_table_where('tbl_deliveries', array('id' => $id), '', 'row');
    $CI->db->select('*,SUM(quantity_stock) as quantitys');
    $CI->db->where('delivery_id', $id);
    $CI->db->group_by('tbl_delivery_items.item_id,tbl_delivery_items.warehouse_id,tbl_delivery_items.location_id,tbl_delivery_items.type_item,lot_code,date_sx,date_sd,date_use');
    $itemss = $CI->db->get('tbl_delivery_items')->result_array();
    usort($itemss, ch_make_cmp(['type_item' => "desc", 'item_id' => "asc"]));
    $CI->db->select('count(*) as count');
    foreach ($itemss as $key => $v) {
        $dem++;
        if ($v['type_item'] == 'products') {
            $v['type_item'] = 'product';
        } else
            if ($v['type_item'] == 'materials') {
            $v['type_item'] = 'nvl';
        } else
                if ($v['type_item'] == 'tools_supplies') {
            $v['type_item'] = 'tools';
        } else
                    if ($v['type_item'] == 'semi_products') {
            $v['type_item'] = 'product';
        }
        $CI->db->or_group_start();
        $CI->db->where('id_items', $v['item_id']);
        if ($v['type_item'] == 'products') {
            $v['type_item'] = 'product';
        }
        $CI->db->where('type_items', $v['type_item']);
        $CI->db->where('localtion', $v['location_id']);
        $CI->db->where('product_quantity >=', $v['quantitys']);
        $CI->db->where('warehouse_id', $v['warehouse_id']);
        $CI->db->where('lot_code', $v['lot_code']);
        $CI->db->where('date_sx', $v['date_sx']);
        $CI->db->where('date_sd', $v['date_sd']);
        $CI->db->where('date_use', $v['date_use']);
        $CI->db->group_end();
    }
    $result = $CI->db->get('tblwarehouse_items')->row();
    if ($result->count == $dem) {
        $data = true;
    } else {
        $data = false;
    }
    return $data;
}
function get_items_deleveries($id)
{
    $dem = 0;
    $CI = &get_instance();
    $main = get_table_where('tbl_deliveries', array('id' => $id), '', 'row');
    $CI->db->select('*,SUM(quantity_stock) as quantitys');
    $CI->db->where('delivery_id', $id);
    $CI->db->group_by('tbl_delivery_items.item_id,tbl_delivery_items.warehouse_id,tbl_delivery_items.type_item,lot_code,date_sx,date_sd,date_use');
    $itemss = $CI->db->get('tbl_delivery_items')->result_array();
    usort($itemss, ch_make_cmp(['type_item' => "desc", 'item_id' => "asc"]));
    foreach ($itemss as $key => $v) {
        if ($v['type_item'] == 'products') {
            $v['type_item'] = 'product';
        } else
            if ($v['type_item'] == 'materials') {
            $v['type_item'] = 'nvl';
        } else
                if ($v['type_item'] == 'tools_supplies') {
            $v['type_item'] = 'tools';
        } else
                    if ($v['type_item'] == 'semi_products') {
            $v['type_item'] = 'product';
        }
        $CI->db->or_group_start();
        $CI->db->where('id_items', $v['item_id']);
        $CI->db->where('type_items', $v['type_item']);
        $CI->db->where('localtion', $v['location_id']);
        $CI->db->where('warehouse_id', $v['warehouse_id']);
        $CI->db->where('lot_code', $v['lot_code']);
        $CI->db->where('date_sx', $v['date_sx']);
        $CI->db->where('date_sd', $v['date_sd']);
        $CI->db->where('date_use', $v['date_use']);
        $CI->db->group_end();
    }
    $result = $CI->db->get('tblwarehouse_items')->result_array();
    foreach ($itemss as $k => $v) {
        $ktr = 0;
        if ($v['type_item'] == 'products') {
            $v['type_item'] = 'product';
        } else
            if ($v['type_item'] == 'materials') {
            $v['type_item'] = 'nvl';
        } else
                if ($v['type_item'] == 'tools_supplies') {
            $v['type_item'] = 'tools';
        } else
                    if ($v['type_item'] == 'semi_products') {
            $v['type_item'] = 'product';
        }
        $itemss[$k]['name_ware'] = get_table_where('tblwarehouse', array('id' => $v['warehouse_id']), '', 'row')->name;
        foreach ($result as $key => $value) {
            if (($v['item_id'] == $value['id_items']) && ($v['warehouse_id'] == $value['warehouse_id']) && ($v['location_id'] == $value['localtion']) && ($v['type_item'] == $value['type_items']) && ($v['lot_code'] == $value['lot_code']) && ($v['date_sx'] == $value['date_sx']) && ($v['date_sd'] == $value['date_sd']) && ($v['date_use'] == $value['date_use'])) {
                $ktr = 1;
                $itemss[$k]['type'] = format_item_purchases($value['type_items']);
                $itemss[$k]['quantity_net'] = $v['quantitys'] - $value['product_quantity'];
            }
        }
        if ($ktr == 0) {
            $itemss[$k]['type'] = format_item_purchases($v['type_item']);
            $itemss[$k]['quantity_net'] = $v['quantitys'];
        }

        if ($itemss[$k]['quantity_net'] <= 0) {
            unset($itemss[$k]);
        }
    }
    return $itemss;
}
function get_items($id = '', $type = '')
{
    if ($type == 'products') {
        $type = 'product';
    } else
        if ($type == 'materials') {
        $type = 'nvl';
    } else
            if ($type == 'tools_supplies') {
        $type = 'tools';
    } else
                if ($type == 'semi_products') {
        $type = 'product';
    }

    $CI = &get_instance();
    $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
    if ($type == 'items') {
        $CI->db->select('tblitems.*,tblunits.unit as unit_name,tblitems.price as price,tblunits.unit as unit_name_stock,tblunits.unit as unit_name_payment,"" as species_name');
        $CI->db->from('tblitems');
        $CI->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
        $CI->db->where('tblitems.id', $id);
        $item = $CI->db->get()->row();
    } elseif ($type == 'tools') {
        $CI->db->select('tbl_tools_supplies.*,tblunits.unit as unit_name,tbl_tools_supplies.price_import  as price,tblunits.unit as unit_name_stock,tblunits.unit as unit_name_payment,"" as species_name');
        $CI->db->from('tbl_tools_supplies');
        $CI->db->join('tblunits', 'tblunits.unitid=tbl_tools_supplies.unit_id', 'left');
        $CI->db->where('tbl_tools_supplies.id', $id);
        $item = $CI->db->get()->row();
    } elseif ($type == 'nvl') {
        $CI->db->select($table . '.*,tblunits.unit as unit_name,' . $table . '.price_import  as price,' . $table . '.images  as avatar,stock_unit.unit as unit_name_stock,payment_unit.unit as unit_name_payment,tbl_species.name as species_name');
        $CI->db->from($table);
        $CI->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
        $CI->db->join('tblunits payment_unit', 'payment_unit.unitid=' . $table . '.unit_payment', 'left');
        $CI->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.standard_unit', 'left');
        $CI->db->join('tbl_species', 'tbl_species.id=' . $table . '.species', 'left');
        $CI->db->where($table . '.id', $id);
        $item = $CI->db->get()->row();
    } elseif ($type == 'product') {
        $CI->db->select($table . '.*,tblunits.unit as unit_name,' . $table . '.price_import  as price,' . $table . '.images  as avatar,stock_unit.unit as unit_name_stock,tblunits.unit as unit_name_payment,tbl_species.name as species_name');
        $CI->db->from($table);
        $CI->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
        $CI->db->join('tblunits stock_unit', 'stock_unit.unitid=' . $table . '.conversion_unit', 'left');
        $CI->db->join('tbl_species', 'tbl_species.id=' . $table . '.species', 'left');
        $CI->db->where($table . '.id', $id);
        $item = $CI->db->get()->row();
    } else {
        $CI->db->select($table . '.*,tblunits.unit as unit_name,' . $table . '.price_import  as price,' . $table . '.images  as avatar,tblunits.unit as unit_name_stock,tblunits.unit as unit_name_payment,"" as species_name');
        $CI->db->from($table);
        $CI->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
        $CI->db->where($table . '.id', $id);
        $item = $CI->db->get()->row();
    }
    $item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/' . $item->avatar) ? base_url('uploads/materials/' . $item->avatar) : (file_exists('uploads/products/' . $item->avatar) ? base_url('uploads/products/' . $item->avatar) : (file_exists('uploads/tools_supplies/' . $item->avatar) ? base_url('uploads/tools_supplies/' . $item->avatar) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
    return $item;
}
function getStart_pay_slip_ch($id_suppliert = NULL, $startDate = NULL)
{
    $CI = &get_instance();
    $total = 0;
    if (is_numeric($id_suppliert) && !empty($startDate)) {
        $CI->db->select_sum('payment');
        $CI->db->where('tblpay_slip.date <', $startDate);
        $pay_slip = $CI->db->get_where('tblpay_slip', array('tblpay_slip.id_supplierss' => $id_suppliert))->row()->payment;

        $CI->db->select_sum('total');
        $CI->db->where('tblother_payslips.date <', $startDate);
        $CI->db->where('tblother_payslips.objects ', 2);
        $CI->db->where('tblother_payslips.type_vouchers != ', 12);
        $other_payslips = $CI->db->get_where('tblother_payslips', array('tblother_payslips.objects_id' => $id_suppliert))->row()->total;
        $total = $pay_slip + $other_payslips;
    }
    return $total;
}
function pay_slip_ch($id_suppliert = NULL, $startDate = NULL, $startEnd = NULL)
{
    $CI = &get_instance();
    $total = 0;
    if (is_numeric($id_suppliert)) {
        $CI->db->select_sum('payment');
        if ($startDate) {
            $CI->db->where('tblpay_slip.date >=', $startDate . ' 00:00:00');
        }
        if ($startEnd) {
            $CI->db->where('tblpay_slip.date <=', $startEnd . ' 23:59:59');
        }
        $pay_slip = $CI->db->get_where('tblpay_slip', array('tblpay_slip.id_supplierss' => $id_suppliert))->row()->payment;

        $CI->db->select_sum('total');
        if ($startDate) {
            $CI->db->where('tblother_payslips.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tblother_payslips.date <=', $startEnd);
        }
        $CI->db->where('tblother_payslips.type_vouchers != ', 12);
        $CI->db->where('tblother_payslips.objects ', 2);
        $other_payslips = $CI->db->get_where('tblother_payslips', array('tblother_payslips.objects_id' => $id_suppliert))->row()->total;
        $total = $pay_slip + $other_payslips;
    }
    return $total;
}
function getStart_debt_supplierts($id_suppliert = NULL, $startDate = NULL)
{
    // array_push($where, 'AND tblpurchase_order.id IN(select id_order from tblimport)');
    $CI = &get_instance();
    $total = 0;
    if (is_numeric($id_suppliert) && !empty($startDate)) {
        $CI->db->select_sum('totalAll_suppliers');
        $CI->db->where('tblpurchase_order.date <', $startDate);
        $CI->db->where('tblpurchase_order.id IN(select id_order from tblimport)');
        $total = $CI->db->get_where('tblpurchase_order', array('tblpurchase_order.suppliers_id' => $id_suppliert))->row()->totalAll_suppliers;
    }
    return $total;
}
function debt_supplierts($id_suppliert = NULL, $startDate = NULL, $startEnd = NULL)
{
    // array_push($where, 'AND tblpurchase_order.id IN(select id_order from tblimport)');
    $CI = &get_instance();
    $total = 0;
    if (is_numeric($id_suppliert)) {
        $CI->db->select_sum('totalAll_suppliers');
        if ($startDate) {
            $CI->db->where('tblpurchase_order.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tblpurchase_order.date <=', $startEnd);
        }
        $CI->db->where('tblpurchase_order.id IN(select id_order from tblimport)');
        $total = $CI->db->get_where('tblpurchase_order', array('tblpurchase_order.suppliers_id' => $id_suppliert))->row()->totalAll_suppliers;
    }
    return $total;
}
function getStartInventory($product_id = NULL, $type = NULL, $warehouse_id = NULL, $startDate = NULL, $localtion = NULL)
{
    $CI = &get_instance();
    $exists_quantity = 0;
    if (is_numeric($product_id) && !empty($startDate) && !empty($type)) {
        if ($type == 'product') {
            $_type = 'products';
        } else
            if ($type == 'nvl') {
            $_type = 'materials';
        } else
                if ($type == 'tools') {
            $_type = 'tools_supplies';
        }
        //Nhap
        $CI->db->select_sum('quantity_stock');
        $CI->db->join('tblimport', 'tblimport.id=tblimport_items.id_import', 'left');
        if ($warehouse_id) {
            $CI->db->where('tblimport.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblimport.warehouseman_date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tblimport_items.localtion_warehouses_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_import = $CI->db->get_where('tblimport_items', array('product_id' => $product_id, 'type' => $type))->row()->quantity_stock;
        // -------------------------------
        // xuất kho
        $CI->db->select_sum('quantity_stock');
        $CI->db->join('tbl_deliveries', 'tbl_deliveries.id=tbl_delivery_items.delivery_id', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbl_delivery_items.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_deliveries.date_warehouseman <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbl_delivery_items.location_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_export = $CI->db->get_where('tbl_delivery_items', array('item_id' => $product_id, 'type_item' => $_type))->row()->quantity_stock;
        // ------------------------------
        // trả hàng ncc
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tblreturn_suppliers', 'tblreturn_suppliers.id=tblreturn_suppliers_items.id_return', 'left');
        if ($warehouse_id) {
            $CI->db->where('tblreturn_suppliers.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblreturn_suppliers.data_warehouseman <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tblreturn_suppliers_items.localtion_warehouses_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_return = $CI->db->get_where('tblreturn_suppliers_items', array('product_id' => $product_id, 'type' => $type))->row()->quantity_net;
        // ------------------------------
        // điều chỉnh tăng
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbladjusted.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbladjusted.date_create <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbladjusted_items.localtion', $localtion);
        }
        $CI->db->where('tbladjusted.type', 1);
        $quantity_adju_T = $CI->db->get_where('tbladjusted_items', array('product_id' => $product_id, 'tbladjusted_items.type' => $type))->row()->quantity_net;
        // ------------------------------
        // điều chỉnh giảm
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbladjusted.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbladjusted.date_create <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbladjusted_items.localtion', $localtion);
        }
        $CI->db->where('tbladjusted.type', 2);
        $quantity_adju_G = $CI->db->get_where('tbladjusted_items', array('product_id' => $product_id, 'tbladjusted_items.type' => $type))->row()->quantity_net;
        // ------------------------------
        // chuyển kho nhận
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbltransfer_warehouse', 'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbltransfer_warehouse_detail.warehouses_to', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbltransfer_warehouse.warehouseman_date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbltransfer_warehouse_detail.localtion_to', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_tranfer_N = $CI->db->get_where('tbltransfer_warehouse_detail', array('id_items' => $product_id, 'type' => $type))->row()->quantity_net;
        // ------------------------------
        // chuyển kho đi
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbltransfer_warehouse', 'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbltransfer_warehouse_detail.warehouses_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbltransfer_warehouse.warehouseman_date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbltransfer_warehouse_detail.localtion_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_tranfer_D = $CI->db->get_where('tbltransfer_warehouse_detail', array('id_items' => $product_id, 'type' => $type))->row()->quantity_net;
        // ------------------------------
        // xuất kho sản xuất
        $CI->db->select_sum('quantity_warehouse');
        $CI->db->join('tbl_suggest_exporting', 'tbl_suggest_exporting.id=tbl_suggest_exporting_items.suggest_exporting_id', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbl_suggest_exporting_items.warehouse_item_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_suggest_exporting.date_warehouseman <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbl_suggest_exporting_items.location_id', $localtion);
        }
        $CI->db->like('type_item', $_type);
        $CI->db->where('warehouseman_id != 0');

        $suggest_exporting_items = $CI->db->get_where('tbl_suggest_exporting_items', array('item_id' => $product_id))->row()->quantity_warehouse;
        // ------------------------------
        // nhập kho thành phẩm
        $CI->db->select_sum('quantity');
        $CI->db->join('tbl_purchase_products', 'tbl_purchase_products.id=tbl_purchase_product_items.purchase_product_id', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbl_purchase_products.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_purchase_products.date_warehouseman <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbl_purchase_product_items.location_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_purchase_products = $CI->db->get_where('tbl_purchase_product_items', array('item_id' => $product_id, 'type_item' => $_type))->row()->quantity;


        // nhập kho phe lieu
        $CI->db->select_sum('quantity');
        $CI->db->join('tbl_purchase_internal', 'tbl_purchase_internal.id=tbl_purchase_internal_items.purchase_internal_id', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbl_purchase_internal.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_purchase_internal.date_warehouseman <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbl_purchase_internal_items.location_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_purchase_internal = $CI->db->get_where('tbl_purchase_internal_items', array('item_id' => $product_id, 'type_item' => $_type))->row()->quantity;

        // ------------------------------
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tblexport_different', 'tblexport_different.id=tbltblexport_different_items.id_export_different', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbltblexport_different_items.warehouses_id', $warehouse_id);
        }
        if (!empty($startDate)) {
            $CI->db->where('tblexport_different.warehouseman_date <', $startDate . ' 00:00:00');
        }

        $CI->db->where('warehouseman_id != 0');
        $quantity_export_different = $CI->db->get_where('tbltblexport_different_items', array('product_id' => $product_id, 'type' => $type))->row()->quantity_net;


        // nhập kho thành phẩm
        $CI->db->select_sum('quantity');
        $CI->db->join('tbl_returned_goods', 'tbl_returned_goods.id=tbl_returned_goods_items.returned_goods_id', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbl_returned_goods_items.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate)) {
            $CI->db->where('tbl_returned_goods.warehouseman_date <', $startDate . ' 00:00:00');
        }
        $CI->db->where('warehouseman_id != 0');

        $returned_goods = $CI->db->get_where('tbl_returned_goods_items', array('item_id' => $product_id, 'type_item' => $_type))->row()->quantity;

        $exists_quantity = $quantity_import - $quantity_export - $quantity_return + $quantity_adju_T - $quantity_adju_G + $quantity_tranfer_N - $quantity_tranfer_D + $quantity_purchase_products - $suggest_exporting_items - $quantity_export_different + $returned_goods + $quantity_purchase_internal;
    }
    return $exists_quantity;
}
function getStartInventory_export($product_id = NULL, $type = NULL, $warehouse_id = NULL, $startDate = NULL, $startEnd = NULL)
{
    $CI = &get_instance();
    $exists_quantity = 0;
    if (is_numeric($product_id) && !empty($type)) {
        if ($type == 'product') {
            $_type = 'products';
        } else
            if ($type == 'nvl') {
            $_type = 'materials';
        } else
                if ($type == 'tools') {
            $_type = 'tools_supplies';
        }
        // xuất kho
        $CI->db->select_sum('quantity_stock');
        $CI->db->join('tbl_deliveries', 'tbl_deliveries.id=tbl_delivery_items.delivery_id', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbl_delivery_items.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbl_deliveries.date >=', $startDate . ' 00:00:00');
            $CI->db->where('tbl_deliveries.date <=', $startEnd . ' 23:59:59');
        }

        $CI->db->where('warehouseman_id != 0');
        $quantity_export = $CI->db->get_where('tbl_delivery_items', array('item_id' => $product_id, 'type_item' => $_type))->row()->quantity_stock;
        // ------------------------------
        // trả hàng ncc
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tblreturn_suppliers', 'tblreturn_suppliers.id=tblreturn_suppliers_items.id_return', 'left');
        if ($warehouse_id) {
            $CI->db->where('tblreturn_suppliers.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tblreturn_suppliers.date >=', $startDate . ' 00:00:00');
            $CI->db->where('tblreturn_suppliers.date <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');


        $quantity_return = $CI->db->get_where('tblreturn_suppliers_items', array('product_id' => $product_id, 'type' => $type))->row()->quantity_net;
        // ------------------------------
        // điều chỉnh giảm
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbladjusted.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbladjusted.date >=', $startDate . ' 00:00:00');
            $CI->db->where('tbladjusted.date <=', $startEnd . ' 23:59:59');
        }

        $CI->db->where('tbladjusted.type', 2);
        $quantity_adju_G = $CI->db->get_where('tbladjusted_items', array('product_id' => $product_id, 'tbladjusted_items.type' => $type))->row()->quantity_net;
        // ------------------------------
        // chuyển kho đi
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbltransfer_warehouse', 'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbltransfer_warehouse_detail.warehouses_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbltransfer_warehouse.date >=', $startDate . ' 00:00:00');
            $CI->db->where('tbltransfer_warehouse.date <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_tranfer_D = $CI->db->get_where('tbltransfer_warehouse_detail', array('id_items' => $product_id, 'type' => $type))->row()->quantity_net;
        // var_dump($CI->db->last_query());die;
        // ------------------------------
        // xuất kho sản xuất
        $CI->db->select_sum('quantity_warehouse');
        $CI->db->join('tbl_suggest_exporting', 'tbl_suggest_exporting.id=tbl_suggest_exporting_items.suggest_exporting_id', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbl_suggest_exporting_items.warehouse_item_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbl_suggest_exporting.date >=', $startDate . ' 00:00:00');
            $CI->db->where('tbl_suggest_exporting.date <=', $startEnd . ' 23:59:59');
        }
        $CI->db->like('type_item', $_type);
        $CI->db->where('warehouseman_id != 0');

        $quantity_purchase_products = $CI->db->get_where('tbl_suggest_exporting_items', array('item_id' => $product_id))->row()->quantity_warehouse;
        // ------------------------------
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tblexport_different', 'tblexport_different.id=tbltblexport_different_items.id_export_different', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbltblexport_different_items.warehouses_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tblexport_different.date >=', $startDate . ' 00:00:00');
            $CI->db->where('tblexport_different.date <=', $startEnd . ' 23:59:59');
        }

        $CI->db->where('warehouseman_id != 0');
        $quantity_export_different = $CI->db->get_where('tbltblexport_different_items', array('product_id' => $product_id, 'type' => $type))->row()->quantity_net;

        $exists_quantity = $quantity_export + $quantity_return + $quantity_adju_G + $quantity_tranfer_D + $quantity_purchase_products + $quantity_export_different;
    }
    return $exists_quantity;
}
function getStartInventory_import($product_id = NULL, $type = NULL, $warehouse_id = NULL, $startDate = NULL, $startEnd = NULL)
{
    $CI = &get_instance();
    $exists_quantity = 0;
    if (is_numeric($product_id) && !empty($type)) {
        if ($type == 'product') {
            $_type = 'products';
        } else
            if ($type == 'nvl') {
            $_type = 'materials';
        } else
                if ($type == 'tools') {
            $_type = 'tools_supplies';
        }
        //Nhap
        $CI->db->select_sum('quantity_stock');
        $CI->db->join('tblimport', 'tblimport.id=tblimport_items.id_import', 'left');
        if ($warehouse_id) {
            $CI->db->where('tblimport.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tblimport.date >=', $startDate . ' 00:00:00');
            $CI->db->where('tblimport.date <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_import = $CI->db->get_where('tblimport_items', array('product_id' => $product_id, 'type' => $type))->row()->quantity_stock;
        // echo '<pre>';print_arrays($CI->db->last_query());die;
        // -------------------------------
        // điều chỉnh tăng
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbladjusted.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbladjusted.date >=', $startDate . ' 00:00:00');
            $CI->db->where('tbladjusted.date <=', $startEnd . ' 23:59:59');
        }

        $CI->db->where('tbladjusted.type', 1);
        $quantity_adju_T = $CI->db->get_where('tbladjusted_items', array('product_id' => $product_id, 'tbladjusted_items.type' => $type))->row()->quantity_net;
        // ------------------------------
        // chuyển kho nhận
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbltransfer_warehouse', 'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbltransfer_warehouse_detail.warehouses_to', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbltransfer_warehouse.date >=', $startDate . ' 00:00:00');
            $CI->db->where('tbltransfer_warehouse.date <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_tranfer_N = $CI->db->get_where('tbltransfer_warehouse_detail', array('id_items' => $product_id, 'type' => $type))->row()->quantity_net;
        // ------------------------------
        // nhập kho thành phẩm
        $CI->db->select_sum('quantity');
        $CI->db->join('tbl_purchase_products', 'tbl_purchase_products.id=tbl_purchase_product_items.purchase_product_id', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbl_purchase_products.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbl_purchase_products.date >=', $startDate . ' 00:00:00');
            $CI->db->where('tbl_purchase_products.date <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_purchase_products = $CI->db->get_where('tbl_purchase_product_items', array('item_id' => $product_id, 'type_item' => $_type))->row()->quantity;

        // nhập kho thành phẩm
        $CI->db->select_sum('quantity');
        $CI->db->join('tbl_returned_goods', 'tbl_returned_goods.id=tbl_returned_goods_items.returned_goods_id', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbl_returned_goods_items.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbl_returned_goods.date >=', $startDate . ' 00:00:00');
            $CI->db->where('tbl_returned_goods.date <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');

        $returned_goods = $CI->db->get_where('tbl_returned_goods_items', array('item_id' => $product_id, 'type_item' => $_type))->row()->quantity;
        // ------------------------------
        $exists_quantity = $quantity_import + $quantity_adju_T + $quantity_tranfer_N + $quantity_purchase_products + $returned_goods;
    }
    return $exists_quantity;
}

function convert_number_to_words($number)
{
    $hyphen = ' ';
    $conjunction = '  ';
    $separator = ' ';
    $negative = 'âm ';
    $decimal = ' phẩy ';
    $dictionary = array(
        0 => 'không',
        1 => 'một',
        2 => 'hai',
        3 => 'ba',
        4 => 'bốn',
        5 => 'năm',
        6 => 'sáu',
        7 => 'bảy',
        8 => 'tám',
        9 => 'chín',
        10 => 'mười',
        11 => 'mười một',
        12 => 'mười hai',
        13 => 'mười ba',
        14 => 'mười bốn',
        15 => 'mười năm',
        16 => 'mười sáu',
        17 => 'mười bảy',
        18 => 'mười tám',
        19 => 'mười chín',
        20 => 'hai mươi',
        30 => 'ba mươi',
        40 => 'bốn mươi',
        50 => 'năm mươi',
        60 => 'sáu mươi',
        70 => 'bảy mươi',
        80 => 'tám mươi',
        90 => 'chín mươi',
        100 => 'trăm',
        1000 => 'nghìn',
        1000000 => 'triệu',
        1000000000 => 'tỷ',
        1000000000000 => 'nghìn tỷ',
        1000000000000000 => 'ngàn triệu triệu',
        1000000000000000000 => 'tỷ tỷ'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error('convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING);
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens = ((int) ($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}
function get_table_where_select($select, $table, $where = array(), $order_by = "", $result = 'result_array', $colum_where = "", $where_in = array())
{
    $CI = &get_instance();
    if ($select != "") {
        $CI->db->select($select);
    }
    if (isset($where) && is_array($where)) {
        $i = 0;
        foreach ($where as $key => $val) {
            if (is_numeric($key)) {
                $CI->db->where($val);
                unset($where[$key]);
            }
            $i++;
        }
        $CI->db->where($where);
    } elseif (strlen($where) > 0) {
        $CI->db->where($where);
    }
    if ($where_in != array() && $colum_where != "") {
        $CI->db->where_in($colum_where, $where_in);
    }
    if ($order_by != "") {
        $CI->db->order_by($order_by);
    }
    $result = $CI->db->get($table)->$result();


    if ($result) {
        return $result;
    } else {
        return array();
    }
}
function sum_from_table_join($table, $attr = array())
{
    if (!isset($attr['field'])) {
        show_error('sum_from_table(); function expect field to be passed.');
    }
    $CI = &get_instance();
    if (isset($attr['where']) && is_array($attr['where'])) {
        $i = 0;
        foreach ($attr['where'] as $key => $val) {
            if (is_numeric($key)) {
                $CI->db->where($val);
                unset($attr['where'][$key]);
            }
            $i++;
        }
        $CI->db->where($attr['where']);
    }
    if (isset($attr['where_or']) && !empty($attr['where_or'])) {
        $CI->db->where($attr['where_or']);
    }
    if (isset($attr['where_in']) && !empty($attr['where_in'])) {
        $CI->db->where_in($attr['where_in']);
    }
    if (isset($attr['join']) && is_array($attr['join'])) {
        foreach ($attr['join'] as $key => $val) {
            $val = explode(',', $val);
            if (count($val) == 3) {
                $CI->db->join($val[0], $val[1], $val[2]);
            } else {
                $CI->db->join($val[0], $val[1]);
            }
        }
    } elseif (strlen($attr['join']) > 0) {
        $attr['join'] = explode(',', $attr['join']);
        if (count($attr['join']) == 3) {
            $CI->db->join($attr['join'][0], $attr['join'][1], $attr['join'][1]);
        } else {
            $CI->db->join($attr['join'][0], $attr['join'][1]);
        }
    }
    // $CI->db->select('product_id,product_quantity,warehouse,warehouseid');
    $CI->db->select_sum($attr['field']);
    $CI->db->from($table);
    $result = $CI->db->get()->row();
    // var_dump($CI->db->last_query());die;
    // echo "<pre>";
    // var_dump($result);die;
    $field = $attr['field'];
    if (strpos($attr['field'], '.') !== false) {
        $field = strafter($attr['field'], '.');
    }
    return $result->{$field};
}

/**
 * General function for all datatables, performs search,additional select,join,where,orders
 * @param  array $aColumns           table columns
 * @param  mixed $sIndexColumn       main column in table for bettter performing
 * @param  string $sTable            table name
 * @param  array  $join              join other tables
 * @param  array  $where             perform where in query
 * @param  array  $additionalSelect  select additional fields
 * @param  string $orderby
 * @param  string $groupBy - note yet tested
 * @return array
 */
function data_tables_init_having_not_search($aColumns, $sIndexColumn, $sTable, $join = array(), $where = array(), $additionalSelect = array(), $orderby = '', $groupBy = '', $having = '', $searchs = false)
{

    $CI = &get_instance();
    $__post = $CI->input->post();

    /*
     * Paging
     */
    $sLimit = "";
    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {
        $sLimit = "LIMIT " . intval($CI->input->post('start')) . ", " . intval($CI->input->post('length'));
    }
    $_aColumns = array();
    foreach ($aColumns as $column) {
        // if found only one dot
        if (substr_count($column, '.') == 1 && strpos($column, ' as ') === false) {
            $_column = explode('.', $column);
            if (isset($_column[1])) {
                if (_startsWith($_column[0], 'tbl')) {
                    $_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);
                    array_push($_aColumns, $_prefix);
                } else {
                    array_push($_aColumns, $column);
                }
            } else {
                array_push($_aColumns, $_column[0]);
            }
        } else {
            array_push($_aColumns, $column);
        }
    }
    /*
     * Ordering
     */
    $sOrder = "";
    if ($CI->input->post('order')) {
        $sOrder = "ORDER BY  ";
        foreach ($CI->input->post('order') as $key => $val) {

            $sOrder .= $aColumns[intval($__post['order'][$key]['column'])];

            $__order_column = $sOrder;
            if (strpos($__order_column, ' as ') !== false) {
                $sOrder = strbefore($__order_column, ' as');
            }
            $_order = strtoupper($__post['order'][$key]['dir']);
            if ($_order == 'ASC') {
                $sOrder .= ' ASC';
            } else {
                $sOrder .= ' DESC';
            }
            $sOrder .= ', ';
        }
        if (trim($sOrder) == "ORDER BY") {
            $sOrder = "";
        }
        if ($sOrder == '' && $orderby != '') {
            $sOrder = $orderby;
        } else {
            $sOrder = substr($sOrder, 0, -2);
        }
    } else {
        $sOrder = $orderby;
    }
    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = "";
    if ((isset($__post['search'])) && $__post['search']['value'] != "") {
        $search_value = $__post['search']['value'];

        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $__search_column = $aColumns[$i];
            if (strpos($__search_column, ' as ') !== false) {
                $__search_column = strbefore($__search_column, ' as');
            }
            if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $sWhere .= $__search_column . " LIKE '%" . $search_value . "%' OR ";
            }
        }
        if (count($additionalSelect) > 0) {
            foreach ($additionalSelect as $searchAdditionalField) {
                if (strpos($searchAdditionalField, ' as ') !== false) {
                    $searchAdditionalField = strbefore($searchAdditionalField, ' as');
                }

                $sWhere .= $searchAdditionalField . " LIKE '%" . $search_value . "%' OR ";
            }
        }
        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    } else {
        // Check for custom filtering
        // $searchFound = 0;
        // $sWhere      = "WHERE (";
        // for ($i = 0; $i < count($aColumns); $i++) {
        //     if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
        //         $search_value    = $__post['columns'][$i]['search']['value'];
        //         $__search_column = $aColumns[$i];
        //         if (strpos($__search_column, ' as ') !== false) {
        //             $__search_column = strbefore($__search_column, ' as');
        //         }
        //         if ($search_value != '') {
        //             $sWhere .= $__search_column . " LIKE '%" . $search_value . "%' OR ";
        //             if (count($additionalSelect) > 0) {
        //                 foreach ($additionalSelect as $searchAdditionalField) {
        //                     $sWhere .= $searchAdditionalField . " LIKE '%" . $search_value . "%' OR ";
        //                 }
        //             }
        //             $searchFound++;
        //         }
        //     }
        // }
        // if ($searchFound > 0) {
        //     $sWhere = substr_replace($sWhere, "", -3);
        //     $sWhere .= ')';
        // } else {
        //     $sWhere = '';
        // }
    }

    /*
     * SQL queries
     * Get data to display
     */
    $_additionalSelect = '';
    if (count($additionalSelect) > 0) {
        $_additionalSelect = ',' . implode(',', $additionalSelect);
    }
    $where = implode(' ', $where);
    if ($sWhere == '') {
        $where = trim($where);
        if (_startsWith($where, 'AND') || _startsWith($where, 'OR')) {
            if (_startsWith($where, 'OR')) {
                $where = substr($where, 2);
            } else {
                $where = substr($where, 3);
            }
            $where = 'WHERE ' . $where;
        }
    }
    $sQuery = "
    SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $_aColumns)) . " " . $_additionalSelect . "
    FROM   $sTable
    " . implode(' ', $join) . "
    $sWhere
    " . $where . "
    $groupBy
    $having
    $sOrder
    $sLimit
    ";
    // return $sQuery;
    // exit($sQuery);
    $rResult = $CI->db->query($sQuery)->result_array();

    /* Data set length after filtering */
    $sQuery = "
    SELECT FOUND_ROWS()
    ";
    $_query = $CI->db->query($sQuery)->result_array();
    $iFilteredTotal = $_query[0]['FOUND_ROWS()'];
    if (_startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3);
    }
    /* Total data set length */
    $sQuery = "
    SELECT COUNT(" . $sTable . '.' . $sIndexColumn . ")
    FROM $sTable " . implode(' ', $join) . ' ' . $where;
    $_query = $CI->db->query($sQuery)->result_array();
    $iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];
    /*
     * Output
     */
    $output = array(
        "draw" => $__post['draw'] ? intval($__post['draw']) : 0,
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );

    return array(
        'rResult' => $rResult,
        'output' => $output
    );
}
function data_tables_init_having($aColumns, $sIndexColumn, $sTable, $join = array(), $where = array(), $additionalSelect = array(), $orderby = '', $groupBy = '', $having = '', $searchs = false)
{

    $CI = &get_instance();
    $__post = $CI->input->post();

    /*
     * Paging
     */
    $sLimit = "";
    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {
        $sLimit = "LIMIT " . intval($CI->input->post('start')) . ", " . intval($CI->input->post('length'));
    }
    $_aColumns = array();
    foreach ($aColumns as $column) {
        // if found only one dot
        if (substr_count($column, '.') == 1 && strpos($column, ' as ') === false) {
            $_column = explode('.', $column);
            if (isset($_column[1])) {
                if (_startsWith($_column[0], 'tbl')) {
                    $_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);
                    array_push($_aColumns, $_prefix);
                } else {
                    array_push($_aColumns, $column);
                }
            } else {
                array_push($_aColumns, $_column[0]);
            }
        } else {
            array_push($_aColumns, $column);
        }
    }
    /*
     * Ordering
     */
    $sOrder = "";
    if ($CI->input->post('order')) {
        $sOrder = "ORDER BY  ";
        foreach ($CI->input->post('order') as $key => $val) {

            $sOrder .= $aColumns[intval($__post['order'][$key]['column'])];

            $__order_column = $sOrder;
            if (strpos($__order_column, ' as ') !== false) {
                $sOrder = strbefore($__order_column, ' as');
            }
            $_order = strtoupper($__post['order'][$key]['dir']);
            if ($_order == 'ASC') {
                $sOrder .= ' ASC';
            } else {
                $sOrder .= ' DESC';
            }
            $sOrder .= ', ';
        }
        if (trim($sOrder) == "ORDER BY") {
            $sOrder = "";
        }
        if ($sOrder == '' && $orderby != '') {
            $sOrder = $orderby;
        } else {
            $sOrder = substr($sOrder, 0, -2);
        }
    } else {
        $sOrder = $orderby;
    }
    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = "";
    if ((isset($__post['search'])) && $__post['search']['value'] != "") {
        $search_value = $__post['search']['value'];

        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $__search_column = $aColumns[$i];
            if (strpos($__search_column, ' as ') !== false) {
                $__search_column = strbefore($__search_column, ' as');
            }
            if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $sWhere .= $__search_column . " LIKE '%" . $search_value . "%' OR ";
            }
        }
        if (count($additionalSelect) > 0) {
            foreach ($additionalSelect as $searchAdditionalField) {
                if (strpos($searchAdditionalField, ' as ') !== false) {
                    $searchAdditionalField = strbefore($searchAdditionalField, ' as');
                }

                $sWhere .= $searchAdditionalField . " LIKE '%" . $search_value . "%' OR ";
            }
        }
        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    } else {
        // Check for custom filtering
        $searchFound = 0;
        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $search_value = $__post['columns'][$i]['search']['value'];
                $__search_column = $aColumns[$i];
                if (strpos($__search_column, ' as ') !== false) {
                    $__search_column = strbefore($__search_column, ' as');
                }
                if ($search_value != '') {
                    $sWhere .= $__search_column . " LIKE '%" . $search_value . "%' OR ";
                    if (count($additionalSelect) > 0) {
                        foreach ($additionalSelect as $searchAdditionalField) {
                            $sWhere .= $searchAdditionalField . " LIKE '%" . $search_value . "%' OR ";
                        }
                    }
                    $searchFound++;
                }
            }
        }
        if ($searchFound > 0) {
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        } else {
            $sWhere = '';
        }
    }

    /*
     * SQL queries
     * Get data to display
     */
    $_additionalSelect = '';
    if (count($additionalSelect) > 0) {
        $_additionalSelect = ',' . implode(',', $additionalSelect);
    }
    $where = implode(' ', $where);
    if ($sWhere == '') {
        $where = trim($where);
        if (_startsWith($where, 'AND') || _startsWith($where, 'OR')) {
            if (_startsWith($where, 'OR')) {
                $where = substr($where, 2);
            } else {
                $where = substr($where, 3);
            }
            $where = 'WHERE ' . $where;
        }
    }
    $sQuery = "
    SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $_aColumns)) . " " . $_additionalSelect . "
    FROM   $sTable
    " . implode(' ', $join) . "
    $sWhere
    " . $where . "
    $groupBy
    $having
    $sOrder
    $sLimit
    ";
    // return $sQuery;
    // exit($sQuery);
    $rResult = $CI->db->query($sQuery)->result_array();

    /* Data set length after filtering */
    $sQuery = "
    SELECT FOUND_ROWS()
    ";
    $_query = $CI->db->query($sQuery)->result_array();
    $iFilteredTotal = $_query[0]['FOUND_ROWS()'];
    if (_startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3);
    }
    /* Total data set length */
    $sQuery = "
    SELECT COUNT(" . $sTable . '.' . $sIndexColumn . ")
    FROM $sTable " . implode(' ', $join) . ' ' . $where;
    $_query = $CI->db->query($sQuery)->result_array();
    $iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];
    /*
     * Output
     */
    $output = array(
        "draw" => $__post['draw'] ? intval($__post['draw']) : 0,
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );

    return array(
        'rResult' => $rResult,
        'output' => $output
    );
}
/**
 * Prefix field name with table ex. table.column
 * @param  string $table
 * @param  string $alias
 * @param  string $field field to check
 * @return string
 */
function get_table_where_sum($table, $where = array(), $field = 'total')
{
    $CI = &get_instance();
    $CI->db->select_sum($field);
    if (isset($where) && is_array($where)) {
        $i = 0;
        foreach ($where as $key => $val) {
            if (is_numeric($key)) {
                $CI->db->where($val);
                unset($where[$key]);
            }
            $i++;
        }
        $CI->db->where($where);
    } elseif (strlen($where) > 0) {
        $CI->db->where($where);
    }
    $result = $CI->db->get($table)->row();
    if ($result) {
        return $result->$field;
    } else {
        return 0;
    }
}
function render_input_ch($name, $label = '', $value = '', $type = 'text', $input_attrs = [], $form_group_attr = [], $form_group_class = '', $input_class = '')
{
    $input = '';
    $_form_group_attr = '';
    $_input_attrs = '';
    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_input_attrs .= $key . '=' . '"' . $val . '" ';
    }

    $_input_attrs = rtrim($_input_attrs);

    $form_group_attr['app-field-wrapper'] = $name;

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }

    $_form_group_attr = rtrim($_form_group_attr);

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $input .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
    }
    $input .= '<div class="input-group">';
    $input .= '<input data-href="' . $value . '" id="run_href" type="submit" id="' . $name . '" name="' . $name . '" class="form-control' . $input_class . '" ' . $_input_attrs . ' value="' . $value . '">';
    $input .= '<div class="input-group-addon">
    <a href="#" onclick="new_link(this);return false;" class="suppliers-field-new"><i id="icon_hau" class="fa fa-plus"></i></a>
    </div>';
    $input .= '</div>';
    $input .= '</div>';
    return $input;
}
function vn_to_str($str)
{

    $unicode = array(

        'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',

        'd' => 'đ',

        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',

        'i' => 'í|ì|ỉ|ĩ|ị',

        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',

        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',

        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',

        'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',

        'D' => 'Đ',

        'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',

        'I' => 'Í|Ì|Ỉ|Ĩ|Ị',

        'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',

        'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',

        'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',

    );

    foreach ($unicode as $nonUnicode => $uni) {
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }
    $str = str_replace(' ', '_', $str);

    return $str;
}

function recursive_Category_Items($id = 0, &$output = null, $parent_id = 0, $indent = null)
{
    $CI = &get_instance();
    $CI->db->select('*');
    $CI->db->from('tblcategories');
    $CI->db->where('tblcategories.category_parent', $parent_id);
    $CI->db->order_by('tblcategories.category_parent');
    $query = $CI->db->get()->result_array();
    foreach ($query as $key => $item) {
        if ($item['category_parent'] == $parent_id) {
            $disabled = '';
            if ($item['id'] == $id && $id != 0) {
                continue;
            }
            $output .= '<option ' . $disabled . '  value="' . $item['id'] . '">' . $indent . '➪ ' . $item['category'] . "</option>";
            recursive_Category_Items($id, $output, $item['id'], $indent . "&nbsp;&nbsp;&nbsp;&nbsp;");
        }
    }
    return $output;
}
function Check_Exists_Items($id = '')
{
    $purchases = get_table_where('tblpurchases_items', array('type' => 'items', 'product_id' => $id), '', 'row');
    $RFQ = get_table_where('tblrfq_ask_price_items', array('type' => 'items', 'product_id' => $id), '', 'row');
    $supplier_quotes = get_table_where('tblsupplier_quote_items', array('type' => 'items', 'product_id' => $id), '', 'row');
    $import = get_table_where('tblimport_items', array('type' => 'items', 'product_id' => $id), '', 'row');
    $purchase_order = get_table_where('tblpurchase_order_items', array('type' => 'items', 'product_id' => $id), '', 'row');
    if (!empty($purchases) || !empty($RFQ) || !empty($supplier_quotes) || !empty($purchase_order) || !empty($import)) {
        return true;
    } else {
        return false;
    }
}
function Check_combo_Items($id = '')
{
    $combo = get_table_where('tblcombo_items', array('product_id' => $id));
    if (!empty($combo)) {
        foreach ($combo as $key => $value) {
            if (Check_Exists_Items($value['rel_id'])) {
                return 1;
            }
        }
        return 2;
    } else {
        return false;
    }
}
function get_supplier_full_name($id = '')
{
    if (is_numeric($id)) {
        $CI = &get_instance();
        $CI->db->where('id', $id);
        $suppliers = $CI->db->get('tblsuppliers')->row();
        return $suppliers->company;
    } else {
        return '';
    }
}
function sumExistsQ_location($localtion, $product_id, $items = array(), $index = 0, $type = '')
{
    $total = 0;
    if (is_numeric($product_id) && isset($items)) {
        for ($i = $index; $i < count($items); $i++) {
            $row = (object) $items[$i];
            if (($row->product_id == $product_id) && ($row->localtion_id == $localtion) && ($row->type == $type)) {
                $total += $row->import_quantity - $row->export_quantity;
            }
            if (($row->product_id != $product_id) && ($row->localtion_id == $localtion) && ($row->type == $type))
                break;
        }
    }
    return $total;
}
function sumExistsQ_all_ch_v1($product_id = '', $items = array(), $index = 0)
{
    $total = 0;
    if (is_numeric($product_id) && isset($items)) {
        for ($i = $index; $i < count($items); $i++) {
            $row = (object) $items[$i];
            if (($row->product_id == $product_id)) {
                $total += $row->import_quantity - $row->export_quantity;
            }
            if (($row->product_id != $product_id))
                break;
        }
    }
    return $total;
}
function sumExistsQ_all_ch_v2($product_id = '', $items = array(), $index = 0, $type = '')
{
    $total = 0;
    if (is_numeric($product_id) && isset($items) && isset($type)) {
        for ($i = $index; $i < count($items); $i++) {
            $row = (object) $items[$i];
            if (($row->product_id == $product_id) && ($row->type == $type)) {
                $total += $row->import_quantity - $row->export_quantity;
            }
            if (($row->product_id != $product_id) && ($row->type != $type))
                break;
        }
    }

    return $total;
}
function sumExistsQ_all_ch($product_id = '', $items = array(), $index = 0)
{
    $total = 0;
    if (is_numeric($product_id) && isset($items)) {
        for ($i = $index; $i < count($items); $i++) {
            $row = (object) $items[$i];
            if ($row->product_id == $product_id) {
                $total += $row->import_quantity - $row->export_quantity;
            }
            if ($row->product_id != $product_id)
                break;
        }
    }

    return $total;
}
function get_full_childs_id($parent_id = '', &$result = array())
{
    $CI = &get_instance();
    array_push($result, $parent_id);
    $CI->db->where('id_parent', $parent_id);
    $items = $CI->db->get('tbllocaltion_warehouses')->result();
    foreach ($items as $value) {
        get_full_childs_id($value->id, $result);
    }
}
function sumExistsQ_all($type = '', $product_id = '', $items = array(), $index = 0)
{
    $total = 0;
    if (is_numeric($product_id) && isset($items)) {
        for ($i = $index; $i < count($items); $i++) {
            if (($items[$i]['product_id'] == $product_id) && ($items[$i]['tblwarehouse_product.type_items'] == $type)) {
                $total += $items[$i]['quantity'];
            }
            if ($items[$i]['product_id'] != $product_id)
                break;
        }
    }
    return $total;
}
function set_status_purchse_order($id = '')
{
    $CI = &get_instance();
    $order = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');

    $get_purchases_items = get_table_where('tblpurchases_items', array('purchases_id' => $order->id_purchase_proce));
    $count = 0;
    foreach ($get_purchases_items as $key => $value) {
        $quantity_net = sum_quantity_order_purchases_all($value['type'], $order->id_purchase_proce, $value['product_id']);
        $quantity = $value['quantity_net'] - $quantity_net;
        // echo '<pre>';
        // var_dump($quantity,$value['product_id']);
        if ($quantity > 0) {
            $count++;
        }
    }
    // die;
    if ($count == 0) {
        $purchases = get_table_where('tblpurchases', array('id' => $order->id_purchase_proce), '', 'row');
        $staff_id = '1foso';
        $date = date('Y-m-d H:i:s');
        $history_status = $purchases->history_status;
        $history_status .= '|' . $staff_id . ',' . $date;
        $in = array(
            'history_status' => $history_status,
            'note_cancel' => '',
            'status' => 4,
        );
        $CI->db->where('id', $order->id_purchase_proce);
        $result = $CI->db->update('tblpurchases', $in);
    } else {
        $ktr_purchases = get_table_where('tblpurchases', array('id' => $order->id_purchase_proce), '', 'row');
        if ($ktr_purchases->status = 4) {
            $cance = explode('|', $ktr_purchases->history_status);
            $cances = explode(',', $cance[3]);
            if ($cances[0] == '1foso') {
                $history_statuss = '';
                $history_status = $ktr_purchases->history_status;
                $history = explode('|', $history_status);
                foreach ($history as $key => $value) {
                    if ($key > 0) {
                        if ($key < 3) {
                            $history_statuss .= '|' . $value;
                        }
                    }
                }
                $in = array(
                    'history_status' => $history_statuss,
                    'note_cancel' => '',
                    'status' => 3,
                );
                $CI->db->where('id', $order->id_purchase_proce);
                $CI->db->update('tblpurchases', $in);
            }
        }
    }
}
function sum_quantity_import($type = '', $id = '', $id_product = '', $plan_id = 0, $idd = '')
{
    $CI = &get_instance();
    $CI->db->select('SUM(tblimport_items.quantity_net) as quantity_net');
    $CI->db->from('tblimport_items');
    $CI->db->join('tblimport', 'tblimport.id=tblimport_items.id_import', 'left');
    $CI->db->where('tblimport.id_order', $id);
    $CI->db->where('product_id', $id_product);
    if (!empty($idd)) {
        $CI->db->where('tblimport_items.id_purchase_order_items', $idd);
    }
    $CI->db->where('type', $type);
    $CI->db->where('tblimport_items.plan_id', $plan_id);
    return $CI->db->get()->row()->quantity_net;
}
function sum_quantity_order($type = '', $id = '', $id_product = '')
{
    $CI = &get_instance();
    $CI->db->select('SUM(tblpurchase_order_items.quantity_suppliers) as quantity_suppliers');
    $CI->db->from('tblpurchase_order_items');
    $CI->db->join('tblpurchase_order', 'tblpurchase_order.id=tblpurchase_order_items.id_purchase_order', 'left');
    $CI->db->where('tblpurchase_order.id_quotes', $id);
    $CI->db->where('product_id', $id_product);
    $CI->db->where('type', $type);
    return $CI->db->get()->row()->quantity_suppliers;
}
function sum_quantity_order_purchases_all($type = '', $id = '', $id_product = '')
{
    $CI = &get_instance();
    $CI->db->select('SUM(tblpurchase_order_items.quantity_suppliers) as quantity_suppliers');
    $CI->db->from('tblpurchase_order_items');
    $CI->db->join('tblpurchase_order', 'tblpurchase_order.id=tblpurchase_order_items.id_purchase_order', 'left');
    $CI->db->where('tblpurchase_order.id_purchase_proce', $id);
    $CI->db->where('product_id', $id_product);
    $CI->db->where('type', $type);
    return $CI->db->get()->row()->quantity_suppliers;
}
function sum_quantity_order_purchases($type = '', $id = '', $id_product = '')
{
    $CI = &get_instance();
    $CI->db->select('SUM(tblpurchase_order_items.quantity_suppliers) as quantity_suppliers');
    $CI->db->from('tblpurchase_order_items');
    $CI->db->join('tblpurchase_order', 'tblpurchase_order.id=tblpurchase_order_items.id_purchase_order', 'left');
    $CI->db->where('tblpurchase_order.id_purchases', $id);
    $CI->db->where('product_id', $id_product);
    $CI->db->where('type', $type);
    return $CI->db->get()->row()->quantity_suppliers;
}
function get_localtion_warehouses_import_excel($where = array(), $lever = NULL)
{
    $CI = &get_instance();
    $CI->db->where('(id_parent is null or id_parent = 0)');
    $CI->db->where('child', 0);
    $CI->db->where('status', 0);
    if (!empty($lever)) {
        $lever = (int) $lever - 1;
        $CI->db->where('lever', $lever);
    }
    if (!empty($where)) {
        $CI->db->where($where);
    }
    $localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
    $string_option = "<option></option>";
    foreach ($localtion as $key => $value) {
        if (!empty($value['id'])) {
            $string_option .= '<option value="' . $value['id'] . '" ' . ($value['child'] ? 'child="' . $value['child'] . '"' : '') . ' data-content="' . $value['name_parent'] . '" content="' . $value['name_parent'] . '">' . $value['name'] . '</option>';
            if (empty($lever)) {
                $string_option .= option_child_localtion_warehouses($value['id']);
            }
        }
    }
    return $string_option;
}
function option_child_localtion_warehouses_import_excel($id_parent = "", $list_array = "<i class='fa fa-caret-right' aria-hidden='true'></i>", &$string_option = "")
{
    $CI = &get_instance();
    if (!empty($id_parent)) {
        $CI->db->where('id_parent', $id_parent);
        $CI->db->where('status', 0);
        $localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
        foreach ($localtion as $key => $value) {
            if (!empty($value['id'])) {
                $string_option .= '<option value="' . $value['id'] . '" ' . ($value['child'] ? ('child="' . $value['child'] . '" content="' . (get_listname_localtion_warehouse($value['id']) . '"')) : '') . ' data-content="' . $list_array . ' ' . $value['name'] . '">' . $value['name'] . '</option>';
                option_child_localtion_warehouses_import_excel($value['id'], ("<i class='fa fa-caret-right' aria-hidden='true'></i>" . $list_array), $string_option);
            }
        }
        return $string_option;
    }
}
function get_localtion_warehouses_kk($where = array(), $lever = NULL, $checked = '')
{
    $CI = &get_instance();
    $CI->db->where('status', 0);
    if (!empty($lever)) {
        $lever = (int) $lever - 1;
        $CI->db->where('lever', $lever);
    } else {
        $CI->db->where('(( child = 1 and id_parent = 0) or ((id_parent is null or id_parent = 0) and child = 0))');
    }
    if (!empty($where)) {
        $CI->db->where($where);
    }
    $CI->db->where('pod_id', 0);
    $CI->db->where('order_id', 0);
    $CI->db->where('productions_plan_id', 0);
    $localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
    $string_option = "<option></option>";
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
    return $string_option;
}
function get_localtion_warehouses($where = array(), $lever = NULL, $checked = '')
{
    $CI = &get_instance();
    $CI->db->where('status', 0);
    if (!empty($lever)) {
        $lever = (int) $lever - 1;
        $CI->db->where('lever', $lever);
    } else {
        $CI->db->where('(( child = 1 and id_parent = 0) or ((id_parent is null or id_parent = 0) and child = 0))');
    }
    if (!empty($where)) {
        $CI->db->where($where);
    }
    $localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
    $string_option = "<option></option>";
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
    return $string_option;
}
function get_localtion_warehouses_new($where = array(), $lever = NULL, $checked = '')
{
    $CI = &get_instance();
    $CI->db->where('status', 0);
    if (!empty($lever)) {
        $lever = (int) $lever - 1;
        $CI->db->where('lever', $lever);
    } else {
        $CI->db->where('(( child = 1 and id_parent = 0) or ((id_parent is null or id_parent = 0) and child = 0))');
    }
    $CI->db->where('pod_id', 0);
    $CI->db->where('order_id', 0);
    $CI->db->where('productions_plan_id', 0);
    if (!empty($where)) {
        $CI->db->where($where);
    }
    $localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
    $string_option = "<option></option>";
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
    $data['html'] = $string_option;
    $data['checked'] = $checked;
    return $data;
}
function option_child_localtion_warehouses($id_parent = "", $list_array = "<i class='fa fa-caret-right' aria-hidden='true'></i>", $checked = '', &$string_option = "")
{
    $CI = &get_instance();
    if (!empty($id_parent)) {
        $CI->db->where('id_parent', $id_parent);
        $CI->db->where('status', 0);
        $localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
        foreach ($localtion as $key => $value) {
            if (!empty($value['id'])) {
                $checkeds = '';

                if ($checked == $value['id']) {
                    $checkeds = 'selected';
                }
                $string_option .= '<option ' . $checkeds . ' value="' . $value['id'] . '" ' . ($value['child'] ? ('child="' . $value['child'] . '" content="' . (get_listname_localtion_warehouse($value['id']) . '"')) : '') . ' data-content="' . $list_array . ' ' . $value['name'] . '">' . $value['name'] . '</option>';
                option_child_localtion_warehouses($value['id'], ("<i class='fa fa-caret-right' aria-hidden='true'></i>" . $list_array), $checked, $string_option);
            }
        }
        return $string_option;
    }
}
function get_options_search_cbo($rel_type = '', $rel_id = '', $type_items = '')
{

    $rel_data = get_relation_data($rel_type, $rel_id, $type_items);
    $rel_val = get_relation_values($rel_data, $rel_type);
    if (!empty($rel_val))
        return array($rel_val);
    return array();
}
function get_listname_localtion_warehouse($id = "")
{
    if (!empty($id)) {
        $CI = &get_instance();
        $CI->db->where('id', $id);
        $localtion = $CI->db->get('tbllocaltion_warehouses')->row();
        if (!empty($localtion)) {
            $string = $localtion->name;
            if (!empty($localtion->id_parent)) {
                return get_parent_localtion_warehouses($localtion->id_parent, $string);
            } else {
                return $string;
            }
        }
    }
    return false;
}
function get_parent_localtion_warehouses($id = "", &$string = "")
{
    $CI = &get_instance();
    if (!empty($id)) {
        $CI->db->where('id', $id);
        $localtion = $CI->db->get('tbllocaltion_warehouses')->row();
        if (!empty($localtion)) {
            $string = $localtion->name . " <i class='fa fa-caret-right text-danger' aria-hidden='true'></i> " . $string;
            if (!empty($localtion->id_parent)) {
                get_parent_localtion_warehouses($localtion->id_parent, $string);
            }
        }
        return $string;
    }
}
function button_child_localtion_warehouses($id_parent = "", $list_array = '<i class="fa fa-caret-right" aria-hidden="true"></i><i class="fa fa-caret-right" aria-hidden="true"></i>', &$string = array('code' => '', 'name' => '', 'date_create' => '', 'status' => '', 'delete' => ''))
{
    $CI = &get_instance();
    if (!empty($id_parent)) {
        $CI->db->where('id_parent', $id_parent);
        $localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
        foreach ($localtion as $key => $value) {
            if (!empty($value['id'])) {
                $string['code'] .= '<p ' . (!empty($value['child']) ? 'class="text-danger"' : '') . '>' . $value['code'] . '</p>';
                $string['name'] .= '<p ' . (!empty($value['child']) ? 'class="text-danger"' : '') . '>' . $list_array . ' ' . $value['name'] . '

                         </p>';
                $string['date_create'] .= '<p>' . _dt($value['date_create']) . '</p>';

                $string['status'] .= '<p class="onoffswitch ' . ($value['status'] == 0 ? 'onoffswitch_ch' : 'onoffswitch_chc') . '" data-switch-url="' . admin_url() . 'warehouse/change_warehouse_localtion_status/' . $value['id'] . '/' . $value['status'] . '" data-toggle="tooltip" data-title="' . _l('') . '">
                <input type="checkbox"' . (!has_permission('warehouse_localtion', '', 'edit') ? ' disabled="disabled"' : '') . ' data-switch-url="' . admin_url() . 'warehouse/change_warehouse_localtion_status" name="onoffswitch" class="onoffswitch-checkbox" id="' . $value['id'] . '" data-id="' . $value['id'] . '" ' . ($value['status'] == 0 ? 'checked' : '') . '>
                <label style="height: 23px;" class="onoffswitch-label" for="' . $value['id'] . '"></label>
                </p>';
                if (has_permission('warehouse_localtion', '', 'delete')) {
                    $string['delete'] .= '<p>' . (($value['child'] == 1 && !exsit_localtion($value['warehouse'], $value['id'])) ? '<a onclick="delete_localtion_warehouses(' . $value['id'] . ')" class="btn btn-danger  btn-icon pull-right" data-toggle="tooltip" data-placement="left"  title=' . _l('ch_delete_localtion') . '>
                            <i class="fa fa-remove"></i>
                        </a>' : '');
                }
                if (has_permission('warehouse_localtion', '', 'edit')) {
                    $string['delete'] .= '
                             <a class="btn btn-default btn-icon pull-right" data-loading-text="<i class=\'fa fa-circle-o-notch fa-spin\'></i> ' . _l('ch_loading') . '" onclick="new_localtion_warehouse(' . $value['id'] . ',this)"><i class="fa fa-pencil-square-o"></i>
                             </a></p>';
                }
                button_child_localtion_warehouses($value['id'], ('<i class="fa fa-caret-right" aria-hidden="true"></i><i class="fa fa-caret-right" aria-hidden="true"></i>' . $list_array . $value['name'] . '<i class="fa fa-caret-right" aria-hidden="true"></i>'), $string);
            }
        }
        return $string;
    }
}
function ch_getProvince($id)
{
    $CI = &get_instance();
    if (isset($id)) {
        $CI->db->where('provinceid', $id);
        return $CI->db->get('province')->row();
    }
    return false;
}
function ch_getDistrict($id)
{
    $CI = &get_instance();
    if (isset($id)) {
        $CI->db->where('districtid', $id);
        return $CI->db->get('district')->row();
    }
    return false;
}
function ch_getWard($id)
{
    $CI = &get_instance();
    if (isset($id)) {
        $CI->db->where('wardid', $id);
        return $CI->db->get('ward')->row();
    }
    return false;
}
function get_fields_export_excel_hau()
{
    $CI = &get_instance();
    $colum_client = $CI->db->list_fields(db_prefix() . 'suppliers');
    $colum_client = array_diff($colum_client, [
        'default_language',
        'default_currency',
        'id_supplier_classify',
    ]);
    $colum_info_client = $CI->db->get(db_prefix() . 'suppliers_info_detail')->result_array();
    return ['colum_client' => $colum_client, 'colum_info_client' => $colum_info_client];
}
function get_fields_export_excel_categories_hau()
{
    $CI = &get_instance();
    $colum_categories = $CI->db->list_fields(db_prefix() . 'categories');
    $colum_categories = array_diff($colum_categories, [
        'code',
    ]);
    return ['colum_categories' => $colum_categories];
}
function get_fields_import_excel_hau()
{
    $CI = &get_instance();
    $colum_client = $CI->db->list_fields(db_prefix() . 'suppliers');
    $colum_client = array_diff($colum_client, [
        'default_language',
        'id',
        'prefix',
        'addedfrom',
        'datecreated',
        'id_supplier_classify',
    ]);

    //TNH
    $colum_client = array_diff($colum_client, [
        'barcode',
        'date_update',
    ]);

    // print_arrays($colum_client);
    $colum_client = [];
    $key = 0;
    $colum_client[++$key] = 'cost_id';
    $colum_client[++$key] = 'groups_in';
    $colum_client[++$key] = 'code';
    $colum_client[++$key] = 'company';
    $colum_client[++$key] = 'abbreviation'; // Tên viết tắt
    $colum_client[++$key] = 'representative'; // Người đại diện

    $colum_client[++$key] = 'vat'; // Mã số thuế
    $colum_client[++$key] = 'code_nxk'; // Mã Số XNK
    $colum_client[++$key] = 'tax'; // % VAT
    $colum_client[++$key] = 'discount_id'; // % Triết Khấu
    $colum_client[++$key] = 'bank_account'; // Số Tài Khoản Ngân Hàng
    $colum_client[++$key] = 'name_account'; // Tên Ngân Hàng
    $colum_client[++$key] = 'address_bank'; // Địa Chỉ Ngân Hàng
    $colum_client[++$key] = 'tm_ck'; // Phương Thức Thanh Toán
    $colum_client[++$key] = 'default_currency'; // Đơn Vị Thanh Toán
    // $colum_client[++$key] = 'currency_conversion_formula'; // Công Thức Chuyển Đổi Tiền
    $colum_client[++$key] = 'time_payment'; // Thời Hạn Thanh Toán
    $colum_client[++$key] = 'contract_number'; // Loại Hợp Đồng
    $colum_client[++$key] = 'number_contract'; // Hợp đồng số
    $colum_client[++$key] = 'renewal_date'; // Ngày Tái Tục
    // $colum_client[++$key] = 'payment_date'; // Ngày thanh toán
    $colum_client[++$key] = 'debt_begin'; // Công nợ đầu kỳ
    // $colum_client[++$key] = 'contact_name'; // Họ và tên người liên hệ
    // $colum_client[++$key] = 'contact_position'; // Chức vụ người liên hệ
    // $colum_client[++$key] = 'phone'; // Số điện thoại
    // $colum_client[++$key] = 'address'; // Địa chỉ
    // $colum_client[++$key] = 'contact_email'; // Email người liên hệ
    $colum_client[++$key] = 'branch'; // Xưởng, Chi Nhánh
    $colum_client[++$key] = 'country'; // Quốc gia
    $colum_client[++$key] = 'certification'; // Chứng nhận
    $colum_client[++$key] = 'price_list_approval'; // Bảng giá Đã duyệt
    $colum_client[++$key] = 'package_specifications'; // Thông số kiện
    $colum_client[++$key] = 'quality_standards'; // Tiêu chuẩn chất lượng
    $colum_client[++$key] = 'packing_regulations'; // Quy định đóng gói
    $colum_client[++$key] = 'date_begin'; // Ngày Bắt Đầu Hoạt Động
    $colum_client[++$key] = 'type_suppliers'; // Loại Nhà Cung Cấp

    $colum_info_client = $CI->db->get(db_prefix() . 'suppliers_info_detail')->result_array();
    return ['colum_client' => $colum_client, 'colum_info_client' => $colum_info_client];
}

function get_fields_import_items_excel_hau()
{
    $CI = &get_instance();
    $colum_client = $CI->db->list_fields(db_prefix() . 'items');
    $colum_client = array_diff($colum_client, [
        'id',
        'prefix',
        'rate',
        'tax',
        'avatar',
        'images_product',
        'active',
        'is_tax',
        'info',
        'country_id',
        'type_items',
        'specification',
        'staff_id',
        'date_create',
        'description',
        'calculated_on_sales',
        'price_import',
    ]);
    return ['colum_items' => $colum_client];
}
function get_status_label_hau($id)
{
    $label = 'default';

    if ($id == 2) {
        $label = 'light-green';
    } else if ($id == 3) {
        $label = 'default';
    } else if ($id == 4) {
        $label = 'info';
    } else if ($id == 5) {
        $label = 'success';
    } else if ($id == 6) {
        $label = 'warning';
    }

    return $label;
}
function format_status_number_ch($id)
{
    $label = get_status_label_hau(6);
    $status_name = $id . ' ' . _l('ch_quote_count');
    $class = 'label label-' . $label;
    return '<span class="inline-block ' . $class . '">' . $status_name . '</span>';
}
function format_status_number_rfq_ch($id)
{
    $label = get_status_label_hau(6);
    $status_name = $id . ' ' . _l('ch_quote_count');
    $class = 'label label-' . $label;
    return $status_name;
}
function format_status_number_suggestion_ch($id)
{
    $label = get_status_label_hau(6);
    $status_name = $id . ' ' . _l('ch_suggestion');
    $class = 'label label-' . $label;
    return $status_name;
}
function format_status_number_import_ch($id)
{
    $label = get_status_label_hau(6);
    $status_name = $id . ' ' . _l('ch_importss');
    $class = 'label label-' . $label;
    return $status_name;
}
function format_status_number_order_ch($id)
{
    $label = get_status_label_hau(6);
    $status_name = $id . ' ' . _l('ch_data_order_t');
    $class = 'label label-' . $label;
    return $status_name;
}
function format_status_number_order_chs($id)
{
    $label = get_status_label_hau(6);
    $status_name = $id . ' ' . _l('Phiếu gia công');
    $class = 'label label-' . $label;
    return $status_name;
}
function format_status_number_invoices_ch($id)
{
    $label = get_status_label_hau(6);
    $status_name = $id . ' ' . _l('client_invoices_tab');
    $class = 'label label-' . $label;
    return $status_name;
}
function format_status_invoice($id)
{

    $label = get_status_label_purchases($id);
    if ($id == 1) {
        $label = 'light-green';
        $item_name = 'Hóa đơn thuế';
    } else if ($id == 2) {
        $label = 'info';
        $item_name = 'Hóa đơn lẻ';
    } else if ($id == 5) {
        $label = 'warning';
        $item_name = 'Phiếu gia công';
    } else if ($id == 6) {
        $label = 'danger';
        $item_name = 'Phiếu dịch vụ';
    }

    $class = 'label label-' . $label;
    return '<span class="inline-block ' . $class . '">' . $item_name . '</span>';
}
function format_status_pay_slip($id)
{

    $label = get_status_label_hau($id);
    if ($id == 2) {
        $label = 'light-green';
        $status_name = _l('ch_status_pays_slip');
    } else if ($id == 1) {
        $label = 'info';
        $status_name = _l('ch_status_pays_slip_part');
    } else if ($id == 0) {
        $label = 'warning';
        $status_name = _l('ch_status_pays_slip_no');
    }
    $class = 'label label-' . $label;
    return '<span class="inline-block ' . $class . '">' . $status_name . '</span>';
}
function format_status_advance($id, $money)
{

    $label = get_status_label_hau($id);
    if ($id == 2) {
        $label = 'light-green';
        $status_name = _l('ch_status_pays_slip');
    } else if ($id == 1) {
        $label = 'info';
        $status_name = _l('Đã tất toán');
    } else if ($id == 0) {
        $label = 'warning';
        $status_name = _l('Chưa tất toán');
    }
    $class = 'label label-' . $label;
    if ($money > 0) {
        return '<span class="inline-block ' . $class . '">' . $status_name . ' (' . formatMoney($money) . ')</span>';
    } else {
        return '<span class="inline-block ' . $class . '">' . $status_name . '</span>';
    }
}
function format_status_pay_slip_new($id, $money)
{

    $label = get_status_label_hau($id);
    if ($id == 2) {
        $label = 'light-green';
        $status_name = _l('ch_status_pays_slip');
    } else if ($id == 1) {
        $label = 'info';
        $status_name = _l('ch_status_pays_slip_part');
    } else if ($id == 0) {
        $label = 'warning';
        $status_name = _l('ch_status_pays_slip_no');
    }
    $class = 'label label-' . $label;
    return '<span class="inline-block ' . $class . '">' . $status_name . ' (' . formatMoney($money, 0) . ')</span>';
}
function format_status_suppler_quote($id)
{

    $label = get_status_label_hau($id);
    if ($id == 2) {
        $label = 'light-green';
        $status_name = _l('ch__quote_ncc');
    } else if ($id == 1) {
        $label = 'info';
        $status_name = _l('ch__YCMH');
    } else if ($id == 0) {
        $label = 'warning';
        $status_name = _l('dont_approve');
    }
    $class = 'label label-' . $label;
    return '<span class="inline-block ' . $class . '">' . $status_name . '</span>';
}
function get_table_where_sum_ch($table, $where = array(), $field = 'total')
{
    $CI = &get_instance();
    $CI->db->select_sum($field);
    if (isset($where) && is_array($where)) {
        $i = 0;
        foreach ($where as $key => $val) {
            if (is_numeric($key)) {
                $CI->db->where($val);
                unset($where[$key]);
            }
            $i++;
        }
        $CI->db->where($where);
    } elseif (strlen($where) > 0) {
        $CI->db->where($where);
    }
    $result = $CI->db->get($table)->row();
    if ($result) {
        return $result->$field;
    } else {
        return 0;
    }
}
function format_purchase_order_father_all_in($id = '', $classes = '', $label = true, $size = '14px')
{
    $status_name = '';
    $purchase_order = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
    if (!empty($purchase_order->id_purchases)) {
        $id_purchases = explode(',', trim($purchase_order->id_purchases, ','));
        $data = '';
        foreach ($id_purchases as $key => $value) {
            $purchase = get_table_where('tblpurchases', array('id' => $purchase_order->id_purchases), '', 'row');
            $data .= $purchase->prefix . $purchase->code . ',';
        }
        $status_name = count($id_purchases) . ' ' . _l('YCMH') . ' :' . trim($data, ',');
        return $status_name;
    } else {
        return;
    }
}
function format_purchase_order_father_in($id = '', $classes = '', $label = true, $size = '14px')
{
    $status_name = '';
    $purchase_order = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
    if (!empty($purchase_order->id_purchases)) {
        $purchase = get_table_where('tblpurchases', array('id' => $purchase_order->id_purchases), '', 'row');
        $status_name = $purchase->prefix . $purchase->code;
        return $status_name;
    } else {
        return;
    }
}
function format_purchase_order_father($id = '', $classes = '', $label = true, $size = '14px')
{
    $status_name = '';
    $purchase_order = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
    if (!empty($purchase_order->id_purchases)) {
        $purchase = get_table_where('tblpurchases', array('id' => $purchase_order->id_purchases), '', 'row');
        $label = 'info';
        $status_name = _l('ch__tuYCMH') . ' :' . $purchase->prefix . '-' . $purchase->code;
        $class = 'label label-' . $label;
        return '<span style="font-size:' . $size . '" class="inline-block ' . $class . '">' . $status_name . '</span>';
    } else {
        return;
    }
}
function format_purchase_order_father_all($id = '', $classes = '', $label = true, $size = '14px')
{
    $status_name = '';
    $purchase_order = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
    if (!empty($purchase_order->id_purchases)) {
        $id_purchases = explode(',', trim($purchase_order->id_purchases, ','));
        $data = '';
        foreach ($id_purchases as $key => $value) {
            $purchase = get_table_where('tblpurchases', array('id' => $value), '', 'row');
            $data .= $purchase->prefix . $purchase->code . ',';
        }
        $label = 'info';
        $status_name = count($id_purchases) . ' ' . _l('YCMH') . ' :' . trim($data, ',');
        $class = 'label label-' . $label;
        return '<span style="font-size:' . $size . '" class="inline-block ' . $class . '">' . $status_name . '</span>';
    } else {
        return;
    }
}
function format_quotes_father($id = '', $classes = '', $label = true, $size = '14px')
{
    $status_name = '';
    $purchase_order = get_table_where('tblsupplier_quotes', array('id' => $id), '', 'row');
    if (!empty($purchase_order->id_purchases)) {
        $purchase = get_table_where('tblpurchases', array('id' => $purchase_order->id_purchases), '', 'row');
        $label = 'info';
        $status_name = _l('ch__tuYCMH') . ' :' . $purchase->prefix . '-' . $purchase->code;
        $class = 'label label-' . $label;
        return '<span style="font-size:' . $size . '" class="inline-block ' . $class . '">' . $status_name . '</span>';
    }
    if (!empty($purchase_order->id_ask_price)) {
        $ask_price = get_table_where('tblrfq_ask_price', array('id' => $purchase_order->id_ask_price), '', 'row');
        $label = 'info';
        $status_name = _l('ch__turfq') . ' :' . $ask_price->prefix . '-' . $ask_price->code;
        $class = 'label label-' . $label;
        return '<span style="font-size:' . $size . '" class="inline-block ' . $class . '">' . $status_name . '</span>';
    }
}
function format_purchase_order_v2($id)
{
    $purchase_order = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
    if (!empty($purchase_order->id_purchases)) {
        if ($purchase_order->check_purchase_all == 1) {
            $id_purchases = explode(',', trim($purchase_order->id_purchases, ','));
            $_data = '';
            foreach ($id_purchases as $k => $v) {
                $purchase = get_table_where('tblpurchases', array('id' => $v), '', 'row');
                $_data .= '<li><a onclick="view_purchases(' . $v . '); return false;" >' . $purchase->prefix . $purchase->code . '</a></li>';
            }
            $_outputStatus = '<div class="dropdown" style="text-align: center;">
                            <button class="dropdown-toggle no_background" style="border: 1px solid #03a9f4;color: #03a9f4;" type="button" data-toggle="dropdown">' . count($id_purchases) . ' YCMH
                            </button>
                            <ul class="dropdown-menu right50">';
            $_outputStatus .= $_data;
            $_outputStatus .= '</ul></div>';
            return $_outputStatus;
            // '<a onclick="view_purchases('.$purchase->id.'); return false;" >' . $purchase->prefix.'-'.$purchase->code.'</a>'.'<br><span class="inline-block ' . $class . '">' . $status_name . '</span>';
        } else {
            $purchase = get_table_where('tblpurchases', array('id' => $purchase_order->id_purchases), '', 'row');
            $label = 'info';
            $status_name = "YCMH";
            $class = 'label label-' . $label;
            return '<a  onclick="view_purchases(' . $purchase->id . '); return false;" ><br><span class="inline-block ' . $class . '">' . $purchase->prefix . $purchase->code . '</span>';
        }
    } elseif (!empty($purchase_order->id_quotes)) {
        $quotes = get_table_where('tblsupplier_quotes', array('id' => $purchase_order->id_quotes), '', 'row');
        $label = 'warning';
        $status_name = _l('estimates');
        $class = 'label label-' . $label;
        return '<a  onclick="view_supplier_quotes(' . $quotes->id . '); return false;" >' . $quotes->prefix . '-' . $quotes->code . '</a>' . '<br><span class="inline-block ' . $class . '">' . $status_name . '</span>';
    } else {
        return;
    }
}
function format_purchase_order($id)
{
    $purchase_order = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
    if (!empty($purchase_order->id_purchases)) {
        if ($purchase_order->check_purchase_all == 1) {
            $id_purchases = explode(',', trim($purchase_order->id_purchases, ','));
            $_data = '';
            foreach ($id_purchases as $k => $v) {
                $purchase = get_table_where('tblpurchases', array('id' => $v), '', 'row');
                $_data .= '<li><a onclick="view_purchases(' . $v . '); return false;" >' . $purchase->prefix . $purchase->code . '</a></li>';
            }
            $_outputStatus = '<div class="dropdown" style="text-align: center;">
                            <button class="dropdown-toggle no_background" style="border: 1px solid #03a9f4;color: #03a9f4;" type="button" data-toggle="dropdown">' . count($id_purchases) . ' YCMH
                            </button>
                            <ul class="dropdown-menu right50">';
            $_outputStatus .= $_data;
            $_outputStatus .= '</ul></div>';
            return $_outputStatus;
            // '<a onclick="view_purchases('.$purchase->id.'); return false;" >' . $purchase->prefix.'-'.$purchase->code.'</a>'.'<br><span class="inline-block ' . $class . '">' . $status_name . '</span>';
        } else {
            $purchase = get_table_where('tblpurchases', array('id' => $purchase_order->id_purchases), '', 'row');
            $label = 'info';
            $status_name = "YCMH";
            $class = 'label label-' . $label;
            return '<a  onclick="view_purchases(' . $purchase->id . '); return false;" >' . $purchase->prefix . $purchase->code . '</a>' . '<br><span class="inline-block ' . $class . '">' . $status_name . '</span>';
        }
    } elseif (!empty($purchase_order->id_quotes)) {
        $quotes = get_table_where('tblsupplier_quotes', array('id' => $purchase_order->id_quotes), '', 'row');
        $label = 'warning';
        $status_name = _l('estimates');
        $class = 'label label-' . $label;
        return '<a  onclick="view_supplier_quotes(' . $quotes->id . '); return false;" >' . $quotes->prefix . '-' . $quotes->code . '</a>' . '<br><span class="inline-block ' . $class . '">' . $status_name . '</span>';
    } else {
        return;
    }
}
function GetThongso($type_items = '', $lot_code = '', $date_sx = '', $date_sd = '', $date_use = '')
{
    if ($type_items == 'tools' || $type_items == 'items') {
        return '<br><span style="font-size: 10px;font-style: italic;">' . _l('Lot') . ': ' . $lot_code . '</span>';
    } else {
        return '<br><span style="font-size: 10px;font-style: italic;">' . _l('Lot') . ': ' . $lot_code . '</span><br><span style="font-size: 10px;font-style: italic;">' . _l('ch_date_of_manufacture_m') . ': ' . (!empty($date_sx) ? _d($date_sx) : '') . '</span><br><span style="font-size: 10px;font-style: italic;">' . _l('ch_items_dateed_m') . ': ' . (!empty($date_sd) ? _d($date_sd) : '') . '</span>';
    }
}
function GetQuycach($id_items = '', $type_items = '')
{
    if ($type_items == 'tools' || $type_items == 'items') {
        return '';
    } else {
        if ($type_items == 'nvl') {
            $items = get_table_where('tbl_materials', array('id' => $id_items), '', 'row_array');
            if (!empty($items)) {
                return '<br><span style="font-size: 10px;font-style: italic;">' . _l('ch_items_specification') . ': ' . $items['mode'] . '</span>';
            }
        }
        if ($type_items == 'product') {
            $items = get_table_where('tbl_products', array('id' => $id_items), '', 'row_array');
            if (!empty($items)) {
                return '<br><span style="font-size: 10px;font-style: italic;">' . _l('ch_items_specification') . ': ' . $items['mode'] . '</span>';
            }
        }
        return '';
    }
}
function get_items_import($id = '')
{
    $count = 0;
    $CI = &get_instance();
    $CI->db->select('tblpurchase_order_items.*')->distinct();
    $CI->db->from('tblpurchase_order_items');
    $CI->db->where('id_purchase_order', $id);
    $item = $CI->db->get()->result_array();
    foreach ($item as $key => $value) {
        $quantity = sum_quantity_import($value['type'], $id, $value['product_id'], $value['plan_id'], $value['id']);
        if (empty($quantity)) {
            $quantity = 0;
        }
        $quantity_net = $value['quantity_suppliers'] - $quantity;

        if ($quantity_net > 0) {
            $count++;
        }
    }
    return $count;
}
function get_items_purchase_new($id = '')
{
    $count = 0;
    $CI = &get_instance();
    $CI->db->select('tblpurchases_items.*')->distinct();
    $CI->db->from('tblpurchases_items');
    $CI->db->where('purchases_id', $id);
    $item = $CI->db->get()->result_array();
    foreach ($item as $key => $value) {
        $quantity_net = $value['quantity_net'] - $value['quantity_create'] - $value['quantity_create_all'];
        if ($quantity_net > 0) {
            $count++;
        }
    }
    return $count;
}

function get_items_purchase_check($id = '')
{
    $count = 0;
    $CI = &get_instance();
    $CI->db->select('tblpurchases_items.*')->distinct();
    $CI->db->from('tblpurchases_items');
    $CI->db->where('purchases_id', $id);
    $item = $CI->db->get()->result_array();
    foreach ($item as $key => $value) {
        if ($value['quantity_create'] == 0 && $value['quantity_create_all'] == 0) {
            $count++;
        }
    }
    return $count;
}
function sum_quantity_quotes($type = '', $id = '', $id_product)
{
    $CI = &get_instance();
    $CI->db->select('SUM(tblsupplier_quote_items.quantity) as quantity');
    $CI->db->from('tblsupplier_quote_items');
    $CI->db->join('tblsupplier_quotes', 'tblsupplier_quotes.id=tblsupplier_quote_items.id_purchase_order', 'left');
    $CI->db->where('tblsupplier_quotes.id_purchases', $id);
    $CI->db->where('product_id', $id_product);
    $CI->db->where('type', $type);
    return $CI->db->get()->row()->quantity;
}
function get_items_purchase_quotes($id = '', $id_order)
{
    $count = 0;
    $CI = &get_instance();
    $CI->db->select('tblpurchases_items.*')->distinct();
    $CI->db->from('tblpurchases_items');
    $CI->db->where('purchases_id', $id);
    $item = $CI->db->get()->result_array();
    foreach ($item as $key => $value) {
        $quantity = sum_quantity_quotes($value['type'], $id_order, $value['product_id']);
        if (empty($quantity)) {
            $quantity = 0;
        }
        $quantity_net = $value['quantity_net'] - $quantity;
        if ($quantity_net > 0) {
            $count++;
        }
    }
    return $count;
}
function get_items_purchase($id = '')
{
    $count = 0;
    $CI = &get_instance();
    $CI->db->select('tblpurchases_items.*,tblitems.name as name_item,tblitems.code as code_item,tblunits.unit as unit_name')->distinct();
    $CI->db->from('tblpurchases_items');
    $CI->db->join('tblitems', 'tblitems.id=tblpurchases_items.product_id AND tblitems.type_items= tblpurchases_items.type', 'left');
    $CI->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
    $CI->db->where('purchases_id', $id);
    $item = $CI->db->get()->result_array();
    foreach ($item as $key => $value) {
        $quantity = sum_quantity($value['type'], $id, $value['product_id']);
        if (empty($quantity)) {
            $quantity = 0;
        }
        $quantity_net = $value['quantity_net'] - $quantity;
        if ($quantity_net > 0) {
            $count++;
        }
    }
    return $count;
}
function sum_quantity($type = '', $id = '', $id_product)
{
    $CI = &get_instance();
    $CI->db->select('SUM(tblpurchase_order_items.quantity) as quantity');
    $CI->db->from('tblpurchase_order_items');
    $CI->db->join('tblpurchase_order', 'tblpurchase_order.id=tblpurchase_order_items.id_purchase_order', 'left');
    $CI->db->where('tblpurchase_order.id_purchases', $id);
    $CI->db->where('product_id', $id_product);
    $CI->db->where('type', $type);
    return $CI->db->get()->row()->quantity;
}
function count_number_PO_ch($id)
{
    // $label = get_status_label_hau(6);
    $status_name = $id . ' ' . _l('ch_po');
    // $class = 'label label-' . $label;
    if ($id > 0) {
        // return '<span class="inline-block ' . $class . '">' . $status_name . '</span>';
        return $status_name;
    } else {
        return;
    }
}
function purchase_order_quote($code = '')
{
    $label = get_status_label_hau(6);
    $status_name = 'Đơn hàng: ' . $code;
    $class = 'label label-' . $label;
    if (!empty($code)) {
        return '<span class="inline-block ' . $class . '">' . $status_name . '</span>';
    } else {
        return;
    }
}
function purchase_quote($code = '')
{
    $label = get_status_label_hau(6);
    $status_name = $code;
    $class = 'label label-' . $label;
    if (!empty($code)) {
        return '<span class="inline-block ' . $class . '">' . $status_name . '</span>';
    } else {
        return;
    }
}
function process_purchases_down($id = '', $type = 0, $string_Row = '')
{
    $data[0] = _l('ch_data_pr');
    $data[1] = _l('ch_data_rfq');
    $data[2] = _l('ch_data_quotes');
    $data[3] = _l('ch_data_order');
    $idd = '';
    // if($type == 0)
    // {
    //  $process = get_table_where('tblpurchases',array('id'=>$id),'','row');
    // }
    if ($type == 1) {
        $process = get_table_where('tblrfq_ask_price', array('id' => $id), '', 'row');
        $idd = $process->id_purchases;
    }
    if ($type == 2) {
        $process = get_table_where('tblsupplier_quotes', array('id' => $id), '', 'row');
        if (!empty($process->id_purchases)) {
            $idd = $process->id_purchases;
        }
        if (!empty($process->id_ask_price)) {
            $idd = $process->id_ask_price;
        }
    }
    if ($type == 3) {
        $process = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
        if (!empty($process->id_purchases)) {
            $idd = $process->id_purchases;
        }
        if (!empty($process->id_quotes)) {
            $idd = $process->id_quotes;
        }
    }
    // var_dump($idd);die;

    if (!empty($idd)) {

        $string_Row .= '<li class="active">';
        $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="1" >';
        $string_Row .= mb_convert_case($data[$type], MB_CASE_TITLE, "UTF-8");
        $string_Row .= '</a>';
        $string_Row .= '</li>';

        $type = $type - 1;
        if ($string_Row != '') {
            var_dump($string_Row);
            return $string_Row;
        }
        $string_Row = process_purchases_down($idd, $type, $string_Row);
    } else {

        return $string_Row;
    }
}
function process_purchases_img($id = '')
{
    $data[0] = _l('ch_data_pr');
    $data[1] = _l('ch_data_rfq');
    $data[2] = _l('ch_data_quotes');
    $data[3] = _l('ch_data_order');
    $data[4] = _l('ch_data_cancel');
    $purchases = get_table_where('tblpurchases', array('id' => $id), '', 'row');
    $cance = explode('|', $purchases->history_status);
    $string_Row = '<ul class="progressbar_img" style="display: flex;flex-direction: row;justify-content: center;">';
    $string_Row .= '<li class="active_img">';
    $string_Row .= staff_profile_image($purchases->staff_create, array('staff-profile-image-small'), 'small');
    $string_Row .= '</li>';
    if (!empty($purchases->process)) {
        $process = explode('|', $purchases->process);
        if ($process[0] == 1) {
            $ask_price = get_table_where('tblrfq_ask_price', array('id' => $process[1]), '', 'row');
            $dataRow = '<span class="inline-block label label-warning">' . $ask_price->prefix . '-' . $ask_price->code . '</span>';
            $string_Row .= '<li class="active_img">';
            $string_Row .= staff_profile_image($ask_price->staff_create, array('staff-profile-image-small'), 'small');
            $string_Row .= '</li>';
            $idquotes = get_table_where('tblsupplier_quotes', array('id_ask_price' => $ask_price->id), '', 'row');
            if (!empty($idquotes)) {
                $process = explode('|', $ask_price->process);
                if (!empty($idquotes)) {
                    $id_quotes = array();
                    $quotes = get_table_where('tblsupplier_quotes', array('id_ask_price' => $ask_price->id));
                    $_data = '';
                    foreach ($quotes as $k => $v) {
                        $order = get_table_where('tblpurchase_order', array('id_quotes' => $v['id']), '', 'row');
                        if (!empty($order)) {
                            $id_quotes[] = $v['id'];
                        }
                    }
                    $supplier_quotes = get_table_where('tblsupplier_quotes', array('id' => $idquotes->id), '', 'row');

                    $string_Row .= '<li class="active_img">';
                    $string_Row .= staff_profile_image($supplier_quotes->staff_create, array('staff-profile-image-small'), 'small');
                    $string_Row .= '</li>';
                    if (!empty($id_quotes)) {
                        if (!empty($id_quotes)) {
                            $order = get_table_where('tblpurchase_order', array('id_quotes' => $id_quotes[0]), '', 'row');

                            if (!empty($order)) {
                                $string_Row .= '<li class="active_img">';
                                $string_Row .= staff_profile_image($order->staff_create, array('staff-profile-image-small'), 'small');
                                $string_Row .= '</li>';
                            } else {
                                $string_Row .= '<li class="active_img">';
                                $string_Row .= staff_profile_image(0, array('staff-profile-image-small'), 'small');
                                $string_Row .= '</li>';
                            }

                            if ($purchases->status == 4) {
                                if (!empty($cance[3])) {
                                    $cances = explode(',', $cance[3]);
                                } else {
                                    $cances = explode(',', $cance[2]);
                                }

                                if ($cances[0] == '1foso') {
                                    $string_Row .= '<li class="cancel">';
                                    $string_Row .= '<img src="' . base_url('uploads/company/' . get_option('company_logo')) . '" class="staff-profile-image-small">';
                                    $string_Row .= '</li>';
                                } else {
                                    $string_Row .= '<li class="cancel">';
                                    $string_Row .= staff_profile_image($cances[0], array('staff-profile-image-small'), 'small');
                                    $string_Row .= '</li>';
                                }
                            }
                        }
                    } else {
                        if ($purchases->status == 4) {
                            $string_Row .= '<li class="cancel">';
                            $string_Row .= '    <a class="pointer red"    status-procedure="1" >';
                            $string_Row .= mb_convert_case($data[4], MB_CASE_TITLE, "UTF-8");
                            $string_Row .= '</a>';
                            $string_Row .= '</li>';
                        } else {
                            $string_Row .= '<li>';
                            $string_Row .= staff_profile_image(0, array('staff-profile-image-small'), 'small');
                            $string_Row .= '</li>';
                        }
                    }
                } else
                    if ($process[0] == 3) {
                    $order = get_table_where('tblpurchase_order', array('id' => $process[1]), '', 'row');
                    $string_Row .= '<li>';
                    $string_Row .= staff_profile_image($order->staff_create, array('staff-profile-image-small'), 'small');
                    $string_Row .= '</li>';
                    if ($purchases->status == 4) {
                        if ($cances[0] == '1foso') {
                            $string_Row .= '<li class="cancel">';
                            $string_Row .= '<img src="' . base_url('uploads/company/' . get_option('company_logo')) . '" class="staff-profile-image-small">';
                            $string_Row .= '</li>';
                        } else {
                            $string_Row .= '<li class="cancel">';
                            $string_Row .= staff_profile_image($cances[0], array('staff-profile-image-small'), 'small');
                            $string_Row .= '</li>';
                        }
                    }
                }
            } else {
                if ($purchases->status == 4) {
                    if (!empty($cance[3])) {
                        $cances = explode(',', $cance[3]);
                    } else {
                        $cances = explode(',', $cance[2]);
                    }

                    if ($cances[0] == '1foso') {
                        $string_Row .= '<li class="cancel">';
                        $string_Row .= '<img src="' . base_url('uploads/company/' . get_option('company_logo')) . '" class="staff-profile-image-small">';
                        $string_Row .= '</li>';
                    } else {
                        $string_Row .= '<li class="cancel">';
                        $string_Row .= staff_profile_image($cances[0], array('staff-profile-image-small'), 'small');
                        $string_Row .= '</li>';
                    }
                } else {
                    $string_Row .= '<li>';
                    $string_Row .= staff_profile_image(0, array('staff-profile-image-small'), 'small');
                    $string_Row .= '</li>';
                    $string_Row .= '<li>';
                    $string_Row .= staff_profile_image(0, array('staff-profile-image-small'), 'small');
                    $string_Row .= '</li>';
                }
            }
        } else
            if ($process[0] == 2) {
            $supplier_quotes = get_table_where('tblsupplier_quotes', array('id' => $process[1]), '', 'row');
            $quotes = $supplier_quotes->prefix . '-' . $supplier_quotes->code;
            $dataRow = '<a href="#" onclick="view_supplier_quotes(' . $supplier_quotes->id . '); return false;" >' . purchase_quote($quotes) . '</a>';
            $string_Row .= '<li class="active_img">';
            $string_Row .= staff_profile_image($supplier_quotes->staff_create, array('staff-profile-image-small'), 'small');
            $string_Row .= '</li>';
            if (!empty($supplier_quotes->process)) {
                $process = explode('|', $supplier_quotes->process);
                if ($process[0] == 3) {
                    $order = get_table_where('tblpurchase_order', array('id' => $process[1]), '', 'row');
                    $string_Row .= '<li class="active_img">';
                    $string_Row .= staff_profile_image($order->staff_create, array('staff-profile-image-small'), 'small');
                    $string_Row .= '</li>';
                    if ($purchases->status == 4) {
                        if (!empty($cance[3])) {
                            $cances = explode(',', $cance[3]);
                        } else {
                            $cances = explode(',', $cance[2]);
                        }

                        if ($cances[0] == '1foso') {
                            $string_Row .= '<li class="cancel">';
                            $string_Row .= '<img src="' . base_url('uploads/company/' . get_option('company_logo')) . '" class="staff-profile-image-small">';
                            $string_Row .= '</li>';
                        } else {
                            $string_Row .= '<li class="cancel">';
                            $string_Row .= staff_profile_image($cances[0], array('staff-profile-image-small'), 'small');
                            $string_Row .= '</li>';
                        }
                    }
                }
            } else {
                if ($purchases->status == 4) {
                    if (!empty($cance[3])) {
                        $cances = explode(',', $cance[3]);
                    } else {
                        $cances = explode(',', $cance[2]);
                    }


                    if ($cances[0] == '1foso') {
                        $string_Row .= '<li class="cancel">';
                        $string_Row .= '<img src="' . base_url('uploads/company/' . get_option('company_logo')) . '" class="staff-profile-image-small">';
                        $string_Row .= '</li>';
                    } else {
                        $string_Row .= '<li class="cancel">';
                        $string_Row .= staff_profile_image($cances[0], array('staff-profile-image-small'), 'small');
                        $string_Row .= '</li>';
                    }
                } else {
                    $string_Row .= '<li>';
                    $string_Row .= staff_profile_image(0, array('staff-profile-image-small'), 'small');
                    $string_Row .= '</li>';
                }
            }
        } else {

            $order = get_table_where('tblpurchase_order', array('id_purchases' => $id), '', 'row');
            if (!empty($order)) {
                $string_Row .= '<li class="active_img">';
                $string_Row .= staff_profile_image($order->staff_create, array('staff-profile-image-small'), 'small');
                $string_Row .= '</li>';
                if ($purchases->status == 4) {
                    if (!empty($cance[3])) {
                        $cances = explode(',', $cance[3]);
                    } else {
                        $cances = explode(',', $cance[2]);
                    }


                    if ($cances[0] == '1foso') {
                        $string_Row .= '<li class="cancel">';
                        $string_Row .= '<img src="' . base_url('uploads/company/' . get_option('company_logo')) . '" class="staff-profile-image-small">';
                        $string_Row .= '</li>';
                    } else {
                        $string_Row .= '<li class="cancel">';
                        $string_Row .= staff_profile_image($cances[0], array('staff-profile-image-small'), 'small');
                        $string_Row .= '</li>';
                    }
                }
            } else {
                $string_Row .= '<li>';
                $string_Row .= staff_profile_image(0, array('staff-profile-image-small'), 'small');
                $string_Row .= '</li>';
                $string_Row .= '<li>';
                $string_Row .= staff_profile_image(0, array('staff-profile-image-small'), 'small');
                $string_Row .= '</li>';
                $string_Row .= '<li>';
                $string_Row .= staff_profile_image(0, array('staff-profile-image-small'), 'small');
                $string_Row .= '</li>';
            }
        }
    } else {
        $order_all = get_table_where('tblpurchases', array('id' => $id), '', 'row');
        $order = get_table_where('tblpurchase_order', array('id_purchases' => $id), '', 'row');
        if (!empty($order)) {
            $string_Row .= '<li class="active_img">';
            $string_Row .= staff_profile_image($order->staff_create, array('staff-profile-image-small'), 'small');
            $string_Row .= '</li>';
            if ($purchases->status == 4) {
                if (!empty($cance[3])) {
                    $cances = explode(',', $cance[3]);
                } else {
                    $cances = explode(',', $cance[2]);
                }
                if ($cances[0] == '1foso') {
                    $string_Row .= '<li class="cancel">';
                    $string_Row .= '<img src="' . base_url('uploads/company/' . get_option('company_logo')) . '" class="staff-profile-image-small">';
                    $string_Row .= '</li>';
                } else {
                    $string_Row .= '<li class="cancel">';
                    $string_Row .= staff_profile_image($cances[0], array('staff-profile-image-small'), 'small');
                    $string_Row .= '</li>';
                }
            }
        } else
            if (!empty($order_all->id_order)) {
            $string_Row .= '<li class="cancel_all">';
            $string_Row .= '<img src="' . base_url('uploads/company/' . get_option('company_logo')) . '" class="staff-profile-image-small">';
            $string_Row .= '</li>';
            if ($purchases->status == 4) {
                if (!empty($cance[3])) {
                    $cances = explode(',', $cance[3]);
                } else {
                    $cances = explode(',', $cance[2]);
                }
                if ($cances[0] == '1foso') {
                    $string_Row .= '<li class="cancel">';
                    $string_Row .= '<img src="' . base_url('uploads/company/' . get_option('company_logo')) . '" class="staff-profile-image-small">';
                    $string_Row .= '</li>';
                } else {
                    $string_Row .= '<li class="cancel">';
                    $string_Row .= staff_profile_image($cances[0], array('staff-profile-image-small'), 'small');
                    $string_Row .= '</li>';
                }
            }
        } else {
            $string_Row .= '<li>';
            $string_Row .= staff_profile_image(0, array('staff-profile-image-small'), 'small');
            $string_Row .= '</li>';
            $string_Row .= '<li>';
            $string_Row .= staff_profile_image(0, array('staff-profile-image-small'), 'small');
            $string_Row .= '</li>';
            $string_Row .= '<li>';
            $string_Row .= staff_profile_image(0, array('staff-profile-image-small'), 'small');
            $string_Row .= '</li>';
        }
    }
    $string_Row .= '<div class="clearfix"></div></ul>';
    return $string_Row;
}
function count_items_quote_rfq($id_purchases = '', $id_rfq = '')
{
    // hauhau
    $purchases = get_table_where('tblpurchases_items', array('purchases_id' => $id_purchases));
    $count_purchases = count($purchases);

    $CI = &get_instance();
    $CI->db->where('tblsupplier_quotes.id_ask_price', $id_rfq);
    $CI->db->join('tblsupplier_quotes', 'tblsupplier_quotes.id = tblsupplier_quote_items.id_supplier_quotes', 'left');
    $CI->db->group_by(array('tblsupplier_quote_items.product_id', 'tblsupplier_quote_items.type'));
    $count_rfq = $CI->db->get('tblsupplier_quote_items')->result_array();
    $count_ = 0;
    if ($count_rfq) {
        $count_ = count($count_rfq);
    }
    return ($count_purchases - $count_);
}
function process_purchases($id = '')
{

    $data[0] = _l('ch_data_pr_v2');
    $data[1] = _l('ch_data_rfq');
    $data[2] = _l('ch_data_quotes');
    $data[3] = _l('ch_data_order');
    $data[4] = _l('ch_data_cancel');
    $string_Row = '<ul class="progressbar" style="display: flex;flex-direction: row;justify-content: center;">';
    $string_Row .= '<li class="active">';
    $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="1" >';
    $string_Row .= $data[0];
    $string_Row .= '</a>';
    $string_Row .= '</li>';
    $purchases = get_table_where('tblpurchases', array('id' => $id), '', 'row');
    if (!empty($purchases->process)) {
        $process = explode('|', $purchases->process);
        if ($process[0] == 1) {
            $ask_price = get_table_where('tblrfq_ask_price', array('id' => $process[1]), '', 'row');
            $dataRow = '<a onclick="rdq_modal(' . $ask_price->id_purchases . ')"><span  style="margin-bottom: 10px;" class="inline-block label label-warning">' . $ask_price->prefix . '-' . $ask_price->code . '</span></a>';
            $string_Row .= '<li class="active">';
            $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="1" >';
            $string_Row .= mb_convert_case($data[$process[0]], MB_CASE_TITLE, "UTF-8");
            $string_Row .= '</a><br><br>' . $dataRow;
            $count_items_quote_rfq = count_items_quote_rfq($id, $ask_price->id);
            if ($count_items_quote_rfq > 0) {
                $string_Row .= '<span class="label" style="background: #e91e63; font-size: 11px;margin-top: 10px">' . count_items_quote_rfq($id, $ask_price->id) . ' ' . _l('mặt hàng chưa tạo báo giá') . '</span>';
            }
            $string_Row .= '</li>';
            $idquotes = get_table_where('tblsupplier_quotes', array('id_ask_price' => $ask_price->id), '', 'row');
            if (!empty($idquotes)) {
                $process = explode('|', $ask_price->process);
                if (!empty($idquotes)) {
                    $id_quotes = array();
                    $supplier_quotes = get_table_where('tblsupplier_quotes', array('id' => $idquotes->id), '', 'row');
                    $quotes = get_table_where('tblsupplier_quotes', array('id_ask_price' => $ask_price->id));
                    $count = count($quotes);
                    $_data = '';
                    foreach ($quotes as $k => $v) {
                        $order = get_table_where('tblpurchase_order', array('id_quotes' => $v['id']), '', 'row');
                        if (!empty($order)) {
                            $id_quotes[] = $v['id'];
                        }
                        $_data .= '<li class="hoang"><a onclick="view_supplier_quotes(' . $v['id'] . '); return false;" >' . $v['prefix'] . '-' . $v['code'] . '</a></li>';
                    }
                    $_outputStatus = '<div class="dropdown" style="text-align: center;">
                                                <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . $count . ' ' . _l('ch_quote_count') . '
                                                </button>
                                                <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso">';
                    $_outputStatus .= $_data;
                    $_outputStatus .= '</ul></div>';



                    $string_Row .= '<li class="active">';
                    $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="2" >';
                    $string_Row .= mb_convert_case($data[$process[0]], MB_CASE_TITLE, "UTF-8");
                    $string_Row .= '</a><br><br>' . $_outputStatus;
                    $string_Row .= '</li>';
                    if (!empty($id_quotes)) {
                        if (!empty($id_quotes)) {
                            // hauhauhau
                            $count = 0;
                            $_data = '';
                            foreach ($id_quotes as $keyorder => $valueorder) {
                                $purchase_order = get_table_where('tblpurchase_order', array('id_quotes' => $valueorder));
                                $count += count($purchase_order);
                                foreach ($purchase_order as $k => $v) {
                                    $_data .= '<li class="hoang"><a onclick="view_purchase_order(' . $v['id'] . '); return false;" >' . $v['prefix'] . '-' . $v['code'] . '</a></li>';
                                }
                            }
                            $_outputStatus = '<div class="dropdown" style="text-align: center;">
                                                <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . count_number_PO_ch($count) . '
                                                </button>
                                                <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso">';
                            $_outputStatus .= $_data;
                            $_outputStatus .= '</ul></div>';
                            $string_Row .= '<li class="active">';
                            $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
                            $string_Row .= mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
                            $string_Row .= '</a><br><br>' . $_outputStatus;
                            $string_Row .= '</li>';
                            if ($purchases->status == 4) {
                                $string_Row .= '<li class="cancel">';
                                $string_Row .= '    <a class="pointer red"    status-procedure="1" >';
                                $string_Row .= mb_convert_case($data[4], MB_CASE_TITLE, "UTF-8");
                                $string_Row .= '</a>';
                                $string_Row .= '</li>';
                            }
                        }
                    } else {
                        if ($purchases->status == 4) {
                            $string_Row .= '<li class="cancel">';
                            $string_Row .= '    <a class="pointer red"    status-procedure="1" >';
                            $string_Row .= mb_convert_case($data[4], MB_CASE_TITLE, "UTF-8");
                            $string_Row .= '</a>';
                            $string_Row .= '</li>';
                        } else {
                            $string_Row .= '<li class="">';
                            $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
                            $string_Row .= mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
                            $string_Row .= '</a>';
                            $string_Row .= '</li>';
                        }
                    }
                } else
                    if ($process[0] == 3) {
                    $string_Row .= '<li class="active">';
                    $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
                    $string_Row .= mb_convert_case($data[$process[0]], MB_CASE_TITLE, "UTF-8");
                    $string_Row .= '</a>';
                    $string_Row .= '</li>';
                    if ($purchases->status == 4) {
                        $string_Row .= '<li class="cancel">';
                        $string_Row .= '    <a class="pointer red"    status-procedure="1" >';
                        $string_Row .= mb_convert_case($data[4], MB_CASE_TITLE, "UTF-8");
                        $string_Row .= '</a>';
                        $string_Row .= '</li>';
                    }
                }
            } else {
                if ($purchases->status == 4) {
                    $string_Row .= '<li class="cancel">';
                    $string_Row .= '    <a class="pointer red"    status-procedure="1" >';
                    $string_Row .= mb_convert_case($data[4], MB_CASE_TITLE, "UTF-8");
                    $string_Row .= '</a>';
                    $string_Row .= '</li>';
                } else {
                    $string_Row .= '<li class="">';
                    $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="2" >';
                    $string_Row .= mb_convert_case($data[2], MB_CASE_TITLE, "UTF-8");
                    $string_Row .= '</a>';
                    $string_Row .= '</li>';
                    $string_Row .= '<li class="">';
                    $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
                    $string_Row .= mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
                    $string_Row .= '</a>';
                    $string_Row .= '</li>';
                }
            }
        } else
            if ($process[0] == 2) {
            $supplier_quotes = get_table_where('tblsupplier_quotes', array('id' => $process[1]), '', 'row');
            $quotes = $supplier_quotes->prefix . '-' . $supplier_quotes->code;
            $dataRow = '<a href="#" onclick="view_supplier_quotes(' . $supplier_quotes->id . '); return false;" >' . purchase_quote($quotes) . '</a>';
            $string_Row .= '<li class="active">';
            $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="2" >';
            $string_Row .= mb_convert_case($data[$process[0]], MB_CASE_TITLE, "UTF-8");
            $string_Row .= '</a><br><br>' . $dataRow;
            $string_Row .= '</li>';
            if (!empty($supplier_quotes->process)) {
                $process = explode('|', $supplier_quotes->process);
                if ($process[0] == 3) {
                    $order = get_table_where('tblpurchase_order', array('id' => $process[1]), '', 'row');
                    $purchase_order = get_table_where('tblpurchase_order', array('id_quotes' => $order->id_quotes));
                    $count = count($purchase_order);
                    $_data = '';
                    foreach ($purchase_order as $k => $v) {
                        $_data .= '<li class="hoang"><a onclick="view_purchase_order(' . $v['id'] . '); return false;" >' . $v['prefix'] . '-' . $v['code'] . '</a></li>';
                    }
                    $_outputStatus = '<div class="dropdown" style="text-align: center;">
                                                <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . count_number_PO_ch($count) . '
                                                </button>
                                                <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso">';
                    $_outputStatus .= $_data;
                    $_outputStatus .= '</ul></div>';
                    $string_Row .= '<li class="active">';
                    $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
                    $string_Row .= mb_convert_case($data[$process[0]], MB_CASE_TITLE, "UTF-8");
                    $string_Row .= '</a><br><br>' . $_outputStatus;
                    $string_Row .= '</li>';
                    if ($purchases->status == 4) {
                        $string_Row .= '<li class="cancel">';
                        $string_Row .= '    <a class="pointer red"    status-procedure="1" >';
                        $string_Row .= mb_convert_case($data[4], MB_CASE_TITLE, "UTF-8");
                        $string_Row .= '</a>';
                        $string_Row .= '</li>';
                    }
                }
            } else {
                if ($purchases->status == 4) {
                    $string_Row .= '<li class="cancel">';
                    $string_Row .= '    <a class="pointer red"    status-procedure="1" >';
                    $string_Row .= mb_convert_case($data[4], MB_CASE_TITLE, "UTF-8");
                    $string_Row .= '</a>';
                    $string_Row .= '</li>';
                } else {
                    $string_Row .= '<li class="">';
                    $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
                    $string_Row .= mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
                    $string_Row .= '</a>';
                    $string_Row .= '</li>';
                }
            }
        } else {
            $order = get_table_where('tblpurchase_order', array('id_purchases' => $id), '', 'row');
            if (!empty($order)) {
                $purchase_order = get_table_where('tblpurchase_order', array('id_purchases' => $order->id_purchases));

                $count = count($purchase_order);
                $_data = '';
                foreach ($purchase_order as $k => $v) {
                    $_data .= '<li class="hoang"><a onclick="view_purchase_order(' . $v['id'] . '); return false;" >' . $v['prefix'] . '-' . $v['code'] . '</a></li>';
                }

                $order_all = get_table_where('tblpurchases', array('id' => $id), '', 'row');

                if (!empty($order_all->id_order)) {
                    $orders = get_table_where('tblpurchase_order', array('id' => $order_all->id_order), '', 'row');
                    $_data .= '<li class="hoang"><a onclick="view_purchase_order(' . $orders->id . '); return false;" >' . $orders->prefix . '-' . $orders->code . '</a></li>';
                    $count = $count + 1;
                }
                $_outputStatus = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . count_number_PO_ch($count) . '
                        </button>
                        <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso" >';
                $_outputStatus .= $_data;
                $_outputStatus .= '</ul></div>';

                $string_Row .= '<li class="active">';
                $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
                $string_Row .= mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
                $string_Row .= '</a><br><br>' . $_outputStatus;
                $string_Row .= '</li>';
                if ($purchases->status == 4) {
                    $string_Row .= '<li class="cancel">';
                    $string_Row .= '    <a class="pointer red"    status-procedure="1" >';
                    $string_Row .= mb_convert_case($data[4], MB_CASE_TITLE, "UTF-8");
                    $string_Row .= '</a>';
                    $string_Row .= '</li>';
                }
            } else {
                $string_Row .= '<li class="">';
                $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="1" >';
                $string_Row .= mb_convert_case($data[1], MB_CASE_TITLE, "UTF-8");
                $string_Row .= '</a>';
                $string_Row .= '</li>';
                $string_Row .= '<li class="">';
                $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="2" >';
                $string_Row .= mb_convert_case($data[2], MB_CASE_TITLE, "UTF-8");
                $string_Row .= '</a>';
                $string_Row .= '</li>';
                $string_Row .= '<li class="">';
                $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
                $string_Row .= mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
                $string_Row .= '</a>';
                $string_Row .= '</li>';
            }
        }
    } else {
        // hau

        $order = get_table_where('tblpurchase_order', array('id_purchases' => $id), '', 'row');
        $order_all = get_table_where('tblpurchases', array('id' => $id), '', 'row');
        if (!empty($order)) {
            $purchase_order = get_table_where('tblpurchase_order', array('id_purchases' => $order->id_purchases));

            $count = count($purchase_order);
            $_data = '';
            foreach ($purchase_order as $k => $v) {
                $_data .= '<li class="hoang"><a onclick="view_purchase_order(' . $v['id'] . '); return false;" >' . $v['prefix'] . '-' . $v['code'] . '</a></li>';
            }


            if (!empty($order_all->id_order)) {
                $orders = get_table_where('tblpurchase_order', array('id' => $order_all->id_order), '', 'row');
                $_data .= '<li class="hoang"><a onclick="view_purchase_order(' . $orders->id . '); return false;" >' . $orders->prefix . '-' . $orders->code . '</a></li>';
                $count = $count + 1;
            }
            $_outputStatus = '<div class="dropdown" style="text-align: center;">
                        <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . count_number_PO_ch($count) . '
                        </button>
                        <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso" >';
            $_outputStatus .= $_data;
            $_outputStatus .= '</ul></div>';

            $string_Row .= '<li class="active">';
            $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
            $string_Row .= mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
            $string_Row .= '</a><br><br>' . $_outputStatus;
            $string_Row .= '</li>';
            if ($purchases->status == 4) {
                $string_Row .= '<li class="cancel">';
                $string_Row .= '    <a class="pointer red"    status-procedure="1" >';
                $string_Row .= mb_convert_case($data[4], MB_CASE_TITLE, "UTF-8");
                $string_Row .= '</a>';
                $string_Row .= '</li>';
            }
        } elseif (!empty($order_all->id_order)) {

            $_data = '';
            $count1 = 0;
            $orders = get_table_where('tblpurchase_order', array('id' => $order_all->id_order), '', 'row');
            $_data .= '<li class="hoang"><a onclick="view_purchase_order(' . $orders->id . '); return false;" >' . $orders->prefix . '-' . $orders->code . '</a></li>';
            $order = get_table_where('tblpurchase_order', array('id_purchases' => $id), '', 'row');
            if (!empty($order)) {
                $purchase_order = get_table_where('tblpurchase_order', array('id_purchases' => $order->id_purchases));
                $count1 = count($purchase_order);
                $_data = '';
                foreach ($purchase_order as $k => $v) {
                    $_data .= '<li class="hoang"><a onclick="view_purchase_order(' . $v['id'] . '); return false;" >' . $v['prefix'] . '-' . $v['code'] . '</a></li>';
                }
            }
            $count = 1 + $count1;
            $_outputStatus = '<div class="dropdown" style="text-align: center;">
                            <button class="dropdown-toggle no_background color_warning" type="button" data-toggle="dropdown">' . count_number_PO_ch($count) . '
                            </button>
                            <ul style="top:unset;bottom:100%;left:unset;right: 12%" class="dropdown-menu ch_foso" >';
            $_outputStatus .= $_data;
            $_outputStatus .= '</ul></div>';

            $string_Row .= '<li class="active">';
            $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
            $string_Row .= mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
            $string_Row .= '</a><br><br>' . $_outputStatus;
            $string_Row .= '</li>';
            if ($purchases->status == 4) {
                $string_Row .= '<li class="cancel">';
                $string_Row .= '    <a class="pointer red"    status-procedure="1" >';
                $string_Row .= mb_convert_case($data[4], MB_CASE_TITLE, "UTF-8");
                $string_Row .= '</a>';
                $string_Row .= '</li>';
            }
        } else {
            $string_Row .= '<li class="">';
            $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="1" >';
            $string_Row .= mb_convert_case($data[1], MB_CASE_TITLE, "UTF-8");
            $string_Row .= '</a>';
            $string_Row .= '</li>';
            $string_Row .= '<li class="">';
            $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="2" >';
            $string_Row .= mb_convert_case($data[2], MB_CASE_TITLE, "UTF-8");
            $string_Row .= '</a>';
            $string_Row .= '</li>';
            $string_Row .= '<li class="">';
            $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
            $string_Row .= mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
            $string_Row .= '</a>';
            $string_Row .= '</li>';
        }
    }
    $string_Row .= '<div class="clearfix"></div></ul>';
    return $string_Row;
}
function render_hau_suppliert($id = '', $id_suppliert = '')
{
    $info_detail = get_table_where('tblsuppliers_info_detail', array('id_suppliers_info' => $id));
    foreach ($info_detail as $key => $value) {
        if (!empty($value['is_required'])) {
            $_input_attrs['data-custom-field-required'] = true;
            $required = 'data-custom-field-required = "1"';
        } else {
            $required = '';
            $_input_attrs = array();
        }
        if ($value['type_form'] == 'input' || $value['type_form'] == 'password') {
            $valueData = get_table_where('tblsuppliers_value', array('id_suppliert' => $id_suppliert, 'id_detail' => $value['id']), '', 'row');

            $_valueData = !empty($valueData->value) ? $valueData->value : '';
            echo render_input('info_detail[' . $value['id'] . ']', $value['name'], $_valueData, $value['type_form'], $_input_attrs);
        } else if ($value['type_form'] == 'radio') {
            $detail = get_table_where('tblsuppliers_info_detail_value', array('id_info_detail' => $value['id']));
            $valueData = get_table_where('tblsuppliers_value', array('id_suppliert' => $id_suppliert, 'id_detail' => $value['id']), '', 'row');
            $_valueData = !empty($valueData->value) ? $valueData->value : '';
            echo '<label class="control-label">' . $value['name'] . '</label>';
            echo '<div class="clearfix"></div>';
            foreach ($detail as $kVal => $vVal) {
                echo "<div class='col-md-6'>";
                echo '    <div class="radio">';
                echo '        <input ' . $required . ' type="radio" id="info_detail[' . $value['id'] . '][' . $kVal . ']" name="info_detail[' . $value['id'] . ']" value="' . $vVal['id'] . '" ' . (($_valueData == $vVal['id']) ? "checked" : "") . '>';
                echo '        <label for="info_detail[' . $value['id'] . '][' . $kVal . ']">' . $vVal['name'] . '</label>';
                echo '    </div>';
                echo "</div>";
            }
        } else if ($value['type_form'] == 'checkbox') {
            $_valueData = array();
            $detail = get_table_where('tblsuppliers_info_detail_value', array('id_info_detail' => $value['id']));
            $valueData = get_table_where('tblsuppliers_value', array('id_suppliert' => $id_suppliert, 'id_detail' => $value['id']));
            foreach ($valueData as $k => $v) {
                $_valueData[] = $v['value'];
            }
            echo '<label class="control-label">' . $value['name'] . '</label>';
            echo '<div class="clearfix"></div>';
            foreach ($detail as $kVal => $vVal) {
                $checked = "";
                if (!empty($_valueData)) {
                    foreach ($_valueData as $Kv => $Vv) {
                        if ($vVal['id'] == $Vv) {
                            $checked = "checked";
                        }
                    }
                }
                echo "<div class='col-md-6'>";
                echo '    <div class="checkbox">';
                echo '        <input ' . $required . ' type="checkbox" id="info_detail[' . $value['id'] . '][' . $kVal . ']" name="info_detail[' . $value['id'] . '][]" value="' . $vVal['id'] . '" ' . $checked . '>';
                echo '        <label for="info_detail[' . $value['id'] . '][' . $kVal . ']">' . $vVal['name'] . '</label>';
                echo '    </div>';
                echo "</div>";
            }
        } else if ($value['type_form'] == 'select') {
            $detail = get_table_where('tblsuppliers_info_detail_value', array('id_info_detail' => $value['id']));
            $valueData = get_table_where('tblsuppliers_value', array('id_suppliert' => $id_suppliert, 'id_detail' => $value['id']), '', 'row');

            $_valueData = !empty($valueData->value) ? $valueData->value : '';
            echo render_select('info_detail[' . $value['id'] . ']', $detail, array('id', 'name'), $value['name'], $_valueData, $_input_attrs);
        } else if ($value['type_form'] == 'select multiple') {
            $detail = get_table_where('tblsuppliers_info_detail_value', array('id_info_detail' => $value['id']));
            $_valueData = array();
            $valueData = get_table_where('tblsuppliers_value', array('id_suppliert' => $id_suppliert, 'id_detail' => $value['id']));
            foreach ($valueData as $k => $v) {
                $_valueData[] = $v['value'];
            }
            echo render_select('info_detail[' . $value['id'] . '][]', $detail, array('id', 'name'), $value['name'], $_valueData, array('multiple' => true), $_input_attrs);
        } else if ($value['type_form'] == 'date') {
            $valueData = get_table_where('tblsuppliers_value', array('id_suppliert' => $id_suppliert, 'id_detail' => $value['id']), '', 'row');

            $_valueData = !empty($valueData->value) ? _d($valueData->value) : '';
            echo render_date_input('info_detail[' . $value['id'] . '][date]', $value['name'], $_valueData, $_input_attrs);
        } else if ($value['type_form'] == 'datetime') {
            $valueData = get_table_where('tblsuppliers_value', array('id_suppliert' => $id_suppliert, 'id_detail' => $value['id']), '', 'row');
            $_valueData = !empty($valueData->value) ? _dt($valueData->value) : '';
            echo render_datetime_input('info_detail[' . $value['id'] . '][datetime]', $value['name'], $_valueData, $_input_attrs);
        }
    }

    // echo '<script>

    //     is_required_lead = is_required_lead.concat('.(!empty($is_required_lead) ? json_encode($is_required_lead) : "{}").');</script>';
}
function get_value_info_suppliers($id_suppliert = '', $id = '', $type = '')
{
    $text = '';
    $detail = array();
    if (($type == 'select multiple') || ($type == 'select')) {

        $detail = get_table_where('tblsuppliers_value', array('id_detail' => $id, 'id_suppliert' => $id_suppliert));
        foreach ($detail as $k => $v) {
            $value = get_table_where('tblsuppliers_info_detail_value', array('id_info_detail' => $id, 'id' => $v['value']), '', 'row');
            if (!empty($value)) {
                $text .= $value->name . ',';
            }
        }
        $text = trim($text, ',');
    }
    if (($type == 'checkbox') || ($type == 'radio')) {

        $detail = get_table_where('tblsuppliers_value', array('id_detail' => $id, 'id_suppliert' => $id_suppliert));
        foreach ($detail as $k => $v) {
            $value = get_table_where('tblsuppliers_info_detail_value', array('id_info_detail' => $id, 'id' => $v['value']), '', 'row');
            if (!empty($value)) {
                $text .= ',' . $value->name;
            }
        }
        $text = trim($text, ',');
    }
    if (($type == 'input')) {

        $detail = get_table_where('tblsuppliers_value', array('id_detail' => $id, 'id_suppliert' => $id_suppliert), '', 'row');
        if (!empty($detail)) {
            $text = $detail->value;
        }
    }
    if (($type == 'date')) {

        $detail = get_table_where('tblsuppliers_value', array('id_detail' => $id, 'id_suppliert' => $id_suppliert), '', 'row');
        if (!empty($detail)) {
            $text = _d($detail->value);
        }
    }
    if (($type == 'datetime')) {

        $detail = get_table_where('tblsuppliers_value', array('id_detail' => $id, 'id_suppliert' => $id_suppliert), '', 'row');
        if (!empty($detail)) {
            $text = _dt($detail->value);
        }
    }
    if (($type == 'password')) {

        $detail = get_table_where('tblsuppliers_value', array('id_detail' => $id, 'id_suppliert' => $id_suppliert), '', 'row');
        if (!empty($detail)) {
            $text = 'Ẩn';
        }
    }
    return $text;
}
function ch_make_cmp(array $sortValues)
{
    return function ($a, $b) use (&$sortValues) {
        foreach ($sortValues as $column => $sortDir) {
            $diff = strcmp($a[$column], $b[$column]);
            $sortDir = strtolower($sortDir);
            if ($diff !== 0) {
                if ('asc' === $sortDir) {
                    return $diff;
                }
                return $diff * -1;
            }
        }
        return 0;
    };
}
function exsit_costs($id = '')
{
    $CI = &get_instance();
    $CI->db->where('id_costs', $id);
    $other_payslips = $CI->db->get('tblother_payslips')->row();

    $CI->db->where('id_costs', $id);
    $pay_slip = $CI->db->get('tblpay_slip')->row();
    if (!empty($other_payslips) || !empty($pay_slip)) {
        return true;
    } else {
        return false;
    }
}
function get_costs($data = '')
{
    $html = '';
    foreach ($data as $key => $value) {
        $strtype = '';
        if ($value['type'] == 1) {
            $strtype = lang('tnh_cpncsx');
        } else if ($value['type'] == 2) {
            $strtype = lang('tnh_cpsxc');
        }

        $html .= '<tr class="treegrid-' . $value['id'] . '">
                <td><h5 style="display: inline-block;">' . ($key + 1) . '</h5></td>
                <td><h5 style="display: inline-block;">' . $value['code'] . '</h5></td>
                <td><h5 style="display: inline-block;">' . $value['name'] . '</h5></td>
                <td>Cấp 1</td>
                <td><h5 style="display: inline-block;">' . $strtype . '</h5></td>';
        if ($value['id'] > 0) {

            $html .= '<td>' . icon_btn('#', 'pencil', 'btn-default', array('onclick' => "edit_costs(" . $value['id'] . ",'" . $value['code'] . "','" . $value['name'] . "', 0, '" . $value['type'] . "'); return false;"));
            $ktr = get_table_where('tblcosts', array('costs_parent' => $value['id']), '', 'row');
            if (empty($ktr) && !exsit_costs($value['id'])) {
                $html .= '<a onclick="delete_costs(' . $value['id'] . ')" class="btn btn-danger  btn-icon " data-toggle="tooltip" data-placement="left">
                                    <i class="fa fa-remove"></i>
                                </a>
                        </td>';
            }
        } else {
            $html .= '<td></td>';
        }
        $html .= '</tr>';
        $_data = get_table_where('tblcosts', array('costs_parent' => $value['id']));
        if ($_data) {
            $html = costs($_data, $html, $value['id'], 2);
        } else {
            continue;
        }
    }
    echo $html;
}
function costs($data = '', $html, $parent, $level)
{
    $strtype = '';

    foreach ($data as $key => $value) {
        if ($value['type'] == 1) {
            $strtype = lang('tnh_cpncsx');
        } else if ($value['type'] == 2) {
            $strtype = lang('tnh_cpsxc');
        }
        $html .= '<tr class="treegrid-' . $value['id'] . ' treegrid-parent-' . $parent . '">
                <td><h5 style="display: inline-block;">' . ($level - 1) . '.' . ($key + 1) . '</h5></td>
                <td><h5 style="display: inline-block;">' . $value['code'] . '</h5></td>
                <td><h5 style="display: inline-block;">' . $value['name'] . '</h5></td>
                <td>Cấp ' . $level . '</td>
                <td><h5 style="display: inline-block;">' . $strtype . '</h5></td>';
        if ($value['id'] > 0) {
            $html .= '<td>' . icon_btn('#', 'pencil', 'btn-default', array('onclick' => "edit_costs(" . $value['id'] . ",'" . $value['code'] . "','" . $value['name'] . "','" . $parent . "', '" . $value['type'] . "'); return false;"));
            $ktr = get_table_where('tblcosts', array('costs_parent' => $value['id']), '', 'row');
            if (empty($ktr) && !exsit_costs($value['id'])) {
                $html .= '<a onclick="delete_costs(' . $value['id'] . ')" class="btn btn-danger  btn-icon " data-toggle="tooltip" data-placement="left">
                                <i class="fa fa-remove"></i>
                            </a>
                    </td>';
            }
        } else {
            $html .= '<td></td>';
        }
        $html .= '</tr>';
        $_data = get_table_where('tblcosts', array('costs_parent' => $value['id']));
        if ($_data) {
            $html = costs($_data, $html, $value['id'], ($level + 1));
        } else {
            continue;
        }
    }
    return $html;
}
function test_quantity_export_different_warehouses($id)
{
    $CI = &get_instance();
    $exporting = get_table_where('tblexport_different', array('id' => $id), '', 'row');
    // $items = get_table_where('tbltblexport_different_items', array('id_export_different' => $id));
    $CI->db->select('*,SUM(quantity_net) as quantity_net');
    $CI->db->where('id_export_different', $id);
    $CI->db->group_by('tbltblexport_different_items.product_id,tbltblexport_different_items.warehouses_id,tbltblexport_different_items.localtion_warehouses_id,tbltblexport_different_items.type,lot_code,date_sx,date_sd,date_use');
    $items = $CI->db->get('tbltblexport_different_items')->result_array();
    $CI->db->select('count(*) as count');
    foreach ($items as $key => $v) {
        $CI->db->or_group_start();
        $CI->db->where('id_items', $v['product_id']);
        $CI->db->where('type_items', $v['type']);
        $CI->db->where('localtion', $v['localtion_warehouses_id']);
        $CI->db->where('product_quantity >=', $v['quantity_net']);
        $CI->db->where('warehouse_id', $v['warehouses_id']);
        $CI->db->where('lot_code', $v['lot_code']);
        $CI->db->where('date_sx', $v['date_sx']);
        $CI->db->where('date_sd', $v['date_sd']);
        $CI->db->where('date_use', $v['date_use']);
        $CI->db->group_end();
    }
    $result = $CI->db->get('tblwarehouse_items')->row();
    if ($result->count == count($items)) {
        $data = true;
    } else {
        $data = false;
    }
    return $data;
}
function get_items_export_different_warehouses($id)
{
    $CI = &get_instance();
    $exporting = get_table_where('tblexport_different', array('id' => $id), '', 'row');
    // $items = get_table_where('tbltblexport_different_items', array('id_export_different' => $id));
    $CI->db->select('*,SUM(quantity_net) as quantity_net');
    $CI->db->where('id_export_different', $id);
    $CI->db->group_by('tbltblexport_different_items.product_id,tbltblexport_different_items.warehouses_id,tbltblexport_different_items.localtion_warehouses_id,tbltblexport_different_items.type,lot_code,date_sx,date_sd,date_use');
    $items = $CI->db->get('tbltblexport_different_items')->result_array();
    foreach ($items as $key => $v) {
        $CI->db->or_group_start();
        $CI->db->where('id_items', $v['product_id']);
        $CI->db->where('type_items', $v['type']);
        $CI->db->where('localtion', $v['localtion_warehouses_id']);
        $CI->db->where('warehouse_id', $v['warehouses_id']);
        $CI->db->where('lot_code', $v['lot_code']);
        $CI->db->where('date_sx', $v['date_sx']);
        $CI->db->where('date_sd', $v['date_sd']);
        $CI->db->where('date_use', $v['date_use']);
        // $CI->db->where('product_quantity <',$v['quantity_exchange']);
        $CI->db->group_end();
    }
    $result = $CI->db->get('tblwarehouse_items')->result_array();

    foreach ($items as $k => $v) {
        $ktr = 0;
        foreach ($result as $key => $value) {
            if (($v['product_id'] == $value['id_items']) && ($v['localtion_warehouses_id'] == $value['localtion']) && ($v['type'] == $value['type_items']) && ($v['warehouses_id'] == $value['warehouse_id']) && ($v['lot_code'] == $value['lot_code']) && ($v['date_sx'] == $value['date_sx']) && ($v['date_sd'] == $value['date_sd']) && ($v['date_use'] == $value['date_use'])) {
                $ktr = 1;
                $items[$k]['type'] = format_item_purchases($value['type_items']);
                $items[$k]['quantity_net'] = $v['quantity_net'] - $value['product_quantity'];
                $get_items = get_items($v['product_id'], $value['type_items']);
                $items[$k]['name'] = $get_items->name;
                $items[$k]['code'] = $get_items->code;
            }
        }
        if ($ktr == 0) {
            $items[$k]['type'] = format_item_purchases($v['type']);
            $items[$k]['quantity_net'] = $v['quantity_net'];
            $get_items = get_items($v['product_id'], $v['type']);
            $items[$k]['name'] = $get_items->name;
            $items[$k]['code'] = $get_items->code;
        }

        if ($items[$k]['quantity_net'] <= 0) {
            unset($items[$k]);
        }
    }
    return $items;
}
function test_quantity_exporting_producion_warehouses($id)
{
    $CI = &get_instance();
    $exporting = get_table_where('tbl_suggest_exporting', array('id' => $id), '', 'row');
    // $items = get_table_where('tbl_suggest_exporting_items',array('suggest_exporting_id'=>$id));
    $CI = &get_instance();
    $CI->db->select('*,SUM(quantity_warehouse) as quantity_exchanges');
    $CI->db->where('suggest_exporting_id', $id);
    $CI->db->group_by('tbl_suggest_exporting_items.item_id,tbl_suggest_exporting_items.type_item,tbl_suggest_exporting_items.location_id,tbl_suggest_exporting_items.warehouse_item_id,lot_code,date_sx,date_sd,date_use');
    $items = $CI->db->get('tbl_suggest_exporting_items')->result_array();
    $CI->db->select('count(*) as count');
    foreach ($items as $key => $v) {
        $CI->db->or_group_start();
        $CI->db->where('id_items', $v['item_id']);
        if ($v['type_item'] == 'products') {
            $v['type_item'] = 'product';
        }
        if ($v['type_item'] == 'semi_products') {
            $v['type_item'] = 'product';
        }
        if ($v['type_item'] == 'materials') {
            $v['type_item'] = 'nvl';
        }
        if ($v['type_item'] == 'tools_supplies') {
            $v['type_item'] = 'tools';
        }
        if ($v['type_item'] == 'semi_products_outside') {
            $v['type_item'] = 'product';
        }
        $CI->db->where('lot_code', $v['lot_code']);
        $CI->db->where('date_sx', $v['date_sx']);
        $CI->db->where('date_sd', $v['date_sd']);
        $CI->db->where('date_use', $v['date_use']);
        $CI->db->where('type_items', $v['type_item']);
        $CI->db->where('localtion', $v['location_id']);
        $CI->db->where('warehouse_id', $v['warehouse_item_id']);
        $CI->db->where('product_quantity >=', (float) $v['quantity_exchanges']);
        $CI->db->group_end();
    }
    $result = $CI->db->get('tblwarehouse_items')->row();

    if ($result->count == count($items)) {
        $data = true;
    } else {
        $data = false;
    }
    return $data;
}
function get_items_quantity_exporting_warehouses($id)
{
    $CI = &get_instance();
    $exporting = get_table_where('tbl_suggest_exporting', array('id' => $id), '', 'row');
    // $items = get_table_where('tbl_suggest_exporting_items',array('suggest_exporting_id'=>$id));
    $CI = &get_instance();
    $CI->db->select('*,SUM(quantity_warehouse) as quantity_exchanges');
    $CI->db->where('suggest_exporting_id', $id);
    $CI->db->group_by('tbl_suggest_exporting_items.item_id,tbl_suggest_exporting_items.type_item,tbl_suggest_exporting_items.location_id,tbl_suggest_exporting_items.warehouse_item_id,lot_code,date_sx,date_sd,date_use');
    $items = $CI->db->get('tbl_suggest_exporting_items')->result_array();
    foreach ($items as $key => $v) {
        $CI->db->or_group_start();
        $CI->db->where('id_items', $v['item_id']);
        if ($v['type_item'] == 'products') {
            $v['type_item'] = 'product';
        }
        if ($v['type_item'] == 'materials') {
            $v['type_item'] = 'nvl';
        }
        if ($v['type_item'] == 'tools_supplies') {
            $v['type_item'] = 'tools';
        }
        if ($v['type_item'] == 'semi_products') {
            $v['type_item'] = 'product';
        }
        if ($v['type_item'] == 'semi_products_outside') {
            $v['type_item'] = 'product';
        }
        $CI->db->where('type_items', $v['type_item']);
        $CI->db->where('localtion', $v['location_id']);
        $CI->db->where('warehouse_id', $v['warehouse_item_id']);
        $CI->db->where('lot_code', $v['lot_code']);
        $CI->db->where('date_sx', $v['date_sx']);
        $CI->db->where('date_sd', $v['date_sd']);
        $CI->db->where('date_use', $v['date_use']);
        // $CI->db->where('product_quantity <',$v['quantity_exchange']);
        $CI->db->group_end();
    }
    // $CI->db->where('warehouse_id',$exporting->warehouse_id);
    $result = $CI->db->get('tblwarehouse_items')->result_array();

    foreach ($items as $k => $v) {
        $ktr = 0;
        if ($v['type_item'] == 'products') {
            $v['type_item'] = 'product';
        }
        if ($v['type_item'] == 'materials') {
            $v['type_item'] = 'nvl';
        }
        if ($v['type_item'] == 'tools_supplies') {
            $v['type_item'] = 'tools';
        }
        if ($v['type_item'] == 'semi_products') {
            $v['type_item'] = 'product';
        }
        if ($v['type_item'] == 'semi_products_outside') {
            $v['type_item'] = 'product';
        }
        foreach ($result as $key => $value) {
            if (($v['item_id'] == $value['id_items']) && ($v['location_id'] == $value['localtion']) && ($v['type_item'] == $value['type_items']) && ($v['lot_code'] == $value['lot_code']) && ($v['date_sx'] == $value['date_sx']) && ($v['date_sd'] == $value['date_sd']) && ($v['date_use'] == $value['date_use'])) {
                $ktr = 1;
                $items[$k]['type'] = format_item_purchases($value['type_items']);
                $items[$k]['quantity_net'] = $v['quantity_exchanges'] - $value['product_quantity'];
            }
        }
        if ($ktr == 0) {
            $items[$k]['type'] = format_item_purchases($v['type_item']);
            $items[$k]['quantity_net'] = $v['quantity_exchanges'];
        }
        $items[$k]['thongso'] = GetThongso($value['type_items'], $value['lot_code'], $value['date_sx'], $value['date_sd'], $value['date_use']);

        if ($items[$k]['quantity_net'] <= 0) {
            unset($items[$k]);
        }
    }
    return $items;
}
function test_quantity_export_warehouses($id)
{
    $CI = &get_instance();
    $items = get_table_where('tbl_export_warehous_items', array('export_warehouse_id' => $id));
    $CI->db->select('count(*) as count');
    foreach ($items as $key => $v) {
        $CI->db->or_group_start();
        $CI->db->where('id_items', $v['item_id']);
        if ($v['type_item'] == 'products') {
            $v['type_item'] = 'product';
        }
        $CI->db->where('type_items', $v['type_item']);
        $CI->db->where('localtion', $v['location_id']);
        $CI->db->where('product_quantity >=', $v['quantity']);
        $CI->db->where('warehouse_id', $v['warehouse_id']);
        $CI->db->group_end();
    }
    $result = $CI->db->get('tblwarehouse_items')->row();
    if ($result->count == count($items)) {
        $data = true;
    } else {
        $data = false;
    }
    return $data;
}
function get_items_export_warehouses($id)
{
    $CI = &get_instance();
    $items = get_table_where('tbl_export_warehous_items', array('export_warehouse_id' => $id));
    foreach ($items as $key => $v) {
        $CI->db->or_group_start();
        $CI->db->where('id_items', $v['item_id']);
        if ($v['type_item'] == 'products') {
            $v['type_item'] = 'product';
        }
        $CI->db->where('type_items', $v['type_item']);
        $CI->db->where('localtion', $v['location_id']);
        // $CI->db->where('product_quantity <',$v['quantity']);
        $CI->db->where('warehouse_id', $v['warehouse_id']);
        $CI->db->group_end();
    }
    $result = $CI->db->get('tblwarehouse_items')->result_array();

    foreach ($items as $k => $v) {
        $ktr = 0;
        if ($v['type_item'] == 'products') {
            $v['type_item'] = 'product';
        }
        foreach ($result as $key => $value) {
            if (($v['item_id'] == $value['id_items']) && ($v['warehouse_id'] == $value['warehouse_id']) && ($v['location_id'] == $value['localtion']) && ($v['type_item'] == $value['type_items'])) {
                $ktr = 1;
                $items[$k]['type'] = format_item_purchases($value['type_items']);
                $items[$k]['quantity_net'] = $v['quantity'] - $value['product_quantity'];
            }
        }
        if ($ktr == 0) {
            $items[$k]['type'] = format_item_purchases($v['type_item']);
            $items[$k]['quantity_net'] = $v['quantity'];
        }

        if ($items[$k]['quantity_net'] <= 0) {
            unset($items[$k]);
        }
    }
    return $items;
}
function test_quantity_tranfer($id)
{
    $dem = 0;
    $CI = &get_instance();
    $main = get_table_where('tbltransfer_warehouse', array('id' => $id), '', 'row');
    $CI->db->select('*,SUM(quantity_net) as quantity_net');
    $CI->db->where('id_transfer', $id);
    $CI->db->group_by('tbltransfer_warehouse_detail.id_items,tbltransfer_warehouse_detail.warehouses_id,tbltransfer_warehouse_detail.localtion_id,tbltransfer_warehouse_detail.type,lot_code,date_sx,date_sd,date_use');
    $itemss = $CI->db->get('tbltransfer_warehouse_detail')->result_array();
    usort($itemss, ch_make_cmp(['type' => "desc", 'id_items' => "asc"]));
    $CI->db->select('count(*) as count');
    foreach ($itemss as $key => $v) {
        $dem++;
        if ($v['type'] == 'products') {
            $v['type'] = 'product';
        }
        if ($v['type'] == 'materials') {
            $v['type'] = 'nvl';
        }
        if ($v['type'] == 'tools_supplies') {
            $v['type'] = 'tools';
        }
        $CI->db->or_group_start();
        $CI->db->where('id_items', $v['id_items']);
        $CI->db->where('type_items', $v['type']);
        $CI->db->where('lot_code', $v['lot_code']);
        $CI->db->where('date_sx', $v['date_sx']);
        $CI->db->where('date_sd', $v['date_sd']);
        $CI->db->where('date_use', $v['date_use']);
        $CI->db->where('localtion', $v['localtion_id']);
        $CI->db->where('product_quantity >=', $v['quantity_net']);
        $CI->db->where('warehouse_id', $v['warehouses_id']);
        $CI->db->group_end();
    }
    $result = $CI->db->get('tblwarehouse_items')->row();
    if ($result->count == $dem) {
        $data = true;
    } else {
        $data = false;
    }
    return $data;

    // $CI =& get_instance();
    //     $tranfer = get_table_where('tbltransfer_warehouse',array('id'=>$id),'','row');
    //     $items = get_table_where('tbltransfer_warehouse_detail',array('id_transfer'=>$id));
    //     $CI->db->select('count(*) as count');
    //     foreach ($items as $key => $v) {
    //         $CI->db->or_group_start();
    //         $CI->db->where('id_items',$v['id_items']);
    //         $CI->db->where('type_items',$v['type']);
    //         $CI->db->where('localtion',$v['localtion_id']);
    //         $CI->db->where('product_quantity >=',$v['quantity_net']);
    //         $CI->db->group_end();
    //     }
    //     $CI->db->where('warehouse_id',$tranfer->warehouse_id);
    //     $result = $CI->db->get('tblwarehouse_items')->row();
    //     if($result->count == count($items))
    //     {
    //         $data = true;
    //     }else
    //     {
    //         $data = false;
    //     }
    //     return $data;
}
function test_quantity_return($id)
{
    $CI = &get_instance();
    $return = get_table_where('tblreturn_suppliers', array('id' => $id), '', 'row');
    $items = get_table_where('tblreturn_suppliers_items', array('id_return' => $id));
    $CI->db->select('count(*) as count');
    foreach ($items as $key => $v) {
        $CI->db->or_group_start();
        $CI->db->where('id_items', $v['product_id']);
        $CI->db->where('type_items', $v['type']);
        $CI->db->where('localtion', $v['localtion_warehouses_id']);
        $CI->db->where('lot_code', $v['lot_code']);
        $CI->db->where('date_sx', $v['date_sx']);
        $CI->db->where('date_sd', $v['date_sd']);
        $CI->db->where('date_use', $v['date_use']);
        $CI->db->where('product_quantity >=', $v['quantity_net']);
        $CI->db->group_end();
    }
    $CI->db->where('warehouse_id', $return->warehouse_id);
    $result = $CI->db->get('tblwarehouse_items')->row();

    $CI->db->select('count(*) as count');
    foreach ($items as $key => $v) {
        $CI->db->or_group_start();
        $CI->db->where('id_items', $v['product_id']);
        $CI->db->where('type_items', $v['type']);
        $CI->db->where('product_quantity <', $v['quantity_net']);
        $CI->db->where('lot_code', $v['lot_code']);
        $CI->db->where('date_sx', $v['date_sx']);
        $CI->db->where('date_sd', $v['date_sd']);
        $CI->db->where('date_use', $v['date_use']);
        $CI->db->group_end();
    }
    $CI->db->where('suppliers_id', $return->suppliers_id);
    $result_v2 = $CI->db->get('tblwarehouse_suppliers')->row();

    if (($result->count == count($items)) && $result_v2->count == 0) {
        $data = true;
    } else {
        $data = false;
    }
    return $data;
}
function get_localtion_warehouses_return($where = array(), $items = NULL, $checked = '', $warehouse = '', $type = '')
{
    $CI = &get_instance();
    $CI->db->where('status', 0);

    $CI->db->where('(( child = 1 and id_parent = 0) or ((id_parent is null or id_parent = 0) and child = 0))');

    if (!empty($where)) {
        $CI->db->where($where);
    }
    $localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
    $string_option = "<option></option>";
    foreach ($localtion as $key => $value) {
        if (!empty($value['id'])) {
            $checkeds = '';
            if ($checked == $value['id']) {
                $checkeds = 'selected';
                $value['child'] = 1;
            }
            $quantity_net = 0;
            $quantity = get_table_where('tblwarehouse_items', array('warehouse_id' => $warehouse, 'id_items' => $items, 'type_items' => $type, 'localtion' => $value['id']), '', 'row');
            if (!empty($quantity)) {
                $quantity_net = $quantity->product_quantity;
            }
            $string_option .= '<option quantity-data=' . $quantity_net . ' ' . $checkeds . ' value="' . $value['id'] . '" ' . ($value['child'] ? 'child="' . $value['child'] . '"' : '') . ' data-content="' . $value['name_parent'] . '" content="' . $value['name_parent'] . '">' . $value['name'] . '</option>';
            if (empty($lever)) {
                $string_option .= option_child_localtion_warehouses_return($value['id'], '', $checked, $items, $warehouse, $type);
            }
        }
    }
    return $string_option;
}
function option_child_localtion_warehouses_return($id_parent = "", $list_array = "<i class='fa fa-caret-right' aria-hidden='true'></i>", $checked = '', $items = '', $warehouse = '', $type = '', &$string_option = "")
{
    $CI = &get_instance();
    if (!empty($id_parent)) {
        $CI->db->where('id_parent', $id_parent);
        $CI->db->where('status', 0);
        $localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
        foreach ($localtion as $key => $value) {
            if (!empty($value['id'])) {
                $checkeds = '';

                if ($checked == $value['id']) {
                    $checkeds = 'selected';
                }
                $quantity_net = 0;
                $quantity = get_table_where('tblwarehouse_items', array('warehouse_id' => $warehouse, 'id_items' => $items, 'type_items' => $type, 'localtion' => $value['id']), '', 'row');
                if (!empty($quantity)) {
                    $quantity_net = $quantity->product_quantity;
                }
                $string_option .= '<option quantity-data=' . $quantity_net . ' ' . $checkeds . ' value="' . $value['id'] . '" ' . ($value['child'] ? ('child="' . $value['child'] . '" content="' . (get_listname_localtion_warehouse($value['id']) . '"')) : '') . ' data-content="' . $list_array . ' ' . $value['name'] . '">' . $value['name'] . '</option>';
                option_child_localtion_warehouses_return($value['id'], ("<i class='fa fa-caret-right' aria-hidden='true'></i>" . $list_array), $checked, $items, $warehouse, $type, $string_option);
            }
        }
        return $string_option;
    }
}
function data_tables_init_nolimt($aColumns, $sIndexColumn, $sTable, $join = [], $where = [], $additionalSelect = [], $sGroupBy = '', $searchAs = [])
{
    $CI = &get_instance();
    $__post = $CI->input->post();
    $havingCount = '';
    /*
     * Paging
     */
    $sLimit = '';

    $_aColumns = [];
    foreach ($aColumns as $column) {
        // if found only one dot
        if (substr_count($column, '.') == 1 && strpos($column, ' as ') === false) {
            $_column = explode('.', $column);
            if (isset($_column[1])) {
                if (startsWith($_column[0], db_prefix())) {
                    $_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);
                    array_push($_aColumns, $_prefix);
                } else {
                    array_push($_aColumns, $column);
                }
            } else {
                array_push($_aColumns, $_column[0]);
            }
        } else {
            array_push($_aColumns, $column);
        }
    }

    /*
     * Ordering
     */
    $nullColumnsAsLast = get_null_columns_that_should_be_sorted_as_last();

    $sOrder = '';
    if ($CI->input->post('order')) {
        $sOrder = 'ORDER BY ';
        foreach ($CI->input->post('order') as $key => $val) {
            //            var_dump(intval($__post['order'][$key]['column']));
            //            var_dump($aColumns);die();
            $columnName = $aColumns[intval($__post['order'][$key]['column'])];
            $dir = strtoupper($__post['order'][$key]['dir']);

            if (strpos($columnName, ' as ') !== false) {
                $columnName = strbefore($columnName, ' as');
            }

            // first checking is for eq tablename.column name
            // second checking there is already prefixed table name in the column name
            // this will work on the first table sorting - checked by the draw parameters
            // in future sorting user must sort like he want and the duedates won't be always last
            if (
                (in_array($sTable . '.' . $columnName, $nullColumnsAsLast)
                    || in_array($columnName, $nullColumnsAsLast))
            ) {
                $sOrder .= $columnName . ' IS NULL ' . $dir . ', ' . $columnName;
            } else {
                $sOrder .= hooks()->apply_filters('datatables_query_order_column', $columnName);
            }
            $sOrder .= ' ' . $dir . ', ';
        }
        if (trim($sOrder) == 'ORDER BY') {
            $sOrder = '';
        }
        $sOrder = rtrim($sOrder, ', ');

        if (
            get_option('save_last_order_for_tables') == '1'
            && $CI->input->post('last_order_identifier')
            && $CI->input->post('order')
        ) {
            // https://stackoverflow.com/questions/11195692/json-encode-sparse-php-array-as-json-array-not-json-object

            $indexedOnly = [];
            foreach ($CI->input->post('order') as $row) {
                $indexedOnly[] = array_values($row);
            }

            $meta_name = $CI->input->post('last_order_identifier') . '-table-last-order';

            update_staff_meta(get_staff_user_id(), $meta_name, json_encode($indexedOnly, JSON_NUMERIC_CHECK));
        }
    }
    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = '';
    if ((isset($__post['search'])) && $__post['search']['value'] != '') {
        $search_value = $__post['search']['value'];
        $search_value = trim($search_value);

        $sWhere = 'WHERE (';
        $sMatchCustomFields = [];
        // Not working, do not use it
        $useMatchForCustomFieldsTableSearch = hooks()->apply_filters('use_match_for_custom_fields_table_search', 'false');

        for ($i = 0; $i < count($aColumns); $i++) {
            $columnName = $aColumns[$i];
            if (strpos($columnName, ' as ') !== false) {
                $columnName = strbefore($columnName, ' as');
            }

            if (stripos($columnName, 'AVG(') !== false || stripos($columnName, 'SUM(') !== false) {
            } else {
                if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == 'true') {
                    if (isset($searchAs[$i])) {
                        $columnName = $searchAs[$i];
                    }
                    // Custom fields values are FULLTEXT and should be searched with MATCH
                    // Not working ATM
                    if ($useMatchForCustomFieldsTableSearch === 'true' && startsWith($columnName, 'ctable_')) {
                        $sMatchCustomFields[] = $columnName;
                    } else {
                        $sWhere .= 'convert(' . $columnName . ' USING utf8)' . " LIKE '%" . $search_value . "%' OR ";
                    }
                }
            }
        }

        if (count($sMatchCustomFields) > 0) {
            foreach ($sMatchCustomFields as $matchCustomField) {
                $sWhere .= "MATCH ({$matchCustomField}) AGAINST (CONVERT(BINARY('{$search_value}') USING utf8)) OR ";
            }
        }

        if (count($additionalSelect) > 0) {
            foreach ($additionalSelect as $searchAdditionalField) {
                if (strpos($searchAdditionalField, ' as ') !== false) {
                    $searchAdditionalField = strbefore($searchAdditionalField, ' as');
                }
                if (stripos($columnName, 'AVG(') !== false || stripos($columnName, 'SUM(') !== false) {
                } else {
                    // Use index
                    $sWhere .= 'convert(' . $searchAdditionalField . ' USING utf8)' . " LIKE '" . $search_value . "%' OR ";
                }
            }
        }
        $sWhere = substr_replace($sWhere, '', -3);
        $sWhere .= ')';
    } else {
        // Check for custom filtering
        $searchFound = 0;
        $sWhere = 'WHERE (';
        for ($i = 0; $i < count($aColumns); $i++) {
            if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == 'true') {
                $search_value = $__post['columns'][$i]['search']['value'];

                $columnName = $aColumns[$i];
                if (strpos($columnName, ' as ') !== false) {
                    $columnName = strbefore($columnName, ' as');
                }
                if ($search_value != '') {
                    $sWhere .= 'convert(' . $columnName . ' USING utf8)' . " LIKE '%" . $search_value . "%' OR ";
                    if (count($additionalSelect) > 0) {
                        foreach ($additionalSelect as $searchAdditionalField) {
                            $sWhere .= 'convert(' . $searchAdditionalField . ' USING utf8)' . " LIKE '" . $search_value . "%' OR ";
                        }
                    }
                    $searchFound++;
                }
            }
        }
        if ($searchFound > 0) {
            $sWhere = substr_replace($sWhere, '', -3);
            $sWhere .= ')';
        } else {
            $sWhere = '';
        }
    }

    /*
     * SQL queries
     * Get data to display
     */
    $_additionalSelect = '';
    if (count($additionalSelect) > 0) {
        $_additionalSelect = ',' . implode(',', $additionalSelect);
    }
    $where = implode(' ', $where);
    if ($sWhere == '') {
        $where = trim($where);
        if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
            if (startsWith($where, 'OR')) {
                $where = substr($where, 2);
            } else {
                $where = substr($where, 3);
            }
            $where = 'WHERE ' . $where;
        }
    }

    $join = implode(' ', $join);

    $sQuery = '
    SELECT SQL_CALC_FOUND_ROWS ' . str_replace(' , ', ' ', implode(', ', $_aColumns)) . ' ' . $_additionalSelect . "
    FROM $sTable
    " . $join . "
    $sWhere
    " . $where . "
    $sGroupBy
    $sOrder
    $sLimit
    ";
    // return $sQuery;
    $rResult = $CI->db->query($sQuery)->result_array();

    $rResult = hooks()->apply_filters('datatables_sql_query_results', $rResult, [
        'table' => $sTable,
        'limit' => $sLimit,
        'order' => $sOrder,
    ]);

    /* Data set length after filtering */
    $sQuery = '
    SELECT FOUND_ROWS()
    ';
    $_query = $CI->db->query($sQuery)->result_array();
    $iFilteredTotal = $_query[0]['FOUND_ROWS()'];
    if (startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3);
    }
    /* Total data set length */
    $sQuery = '
    SELECT COUNT(' . $sTable . '.' . $sIndexColumn . ")
    FROM $sTable " . $join . ' ' . $where;

    $_query = $CI->db->query($sQuery)->result_array();
    $iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];
    /*
     * Output
     */
    $output = [
        'draw' => $__post['draw'] ? intval($__post['draw']) : 0,
        'iTotalRecords' => $iTotal,
        'iTotalDisplayRecords' => $iFilteredTotal,
        'aaData' => [],
    ];

    return [
        'rResult' => $rResult,
        'output' => $output,
    ];
}
function getStart_payslips($id_payment_modes = NULL, $startDate = NULL, $EndDate = NULL)
{
    $CI = &get_instance();
    $total = 0;
    $total_v1 = 0;
    if (is_numeric($id_payment_modes) && !empty($startDate)) {
        $CI->db->select_sum('payment');
        if (!empty($EndDate)) {
            $CI->db->where('tblpay_slip.day_vouchers >=', $startDate);
            $CI->db->where('tblpay_slip.day_vouchers <=', $EndDate);
        } else {
            $CI->db->where('tblpay_slip.day_vouchers <', $startDate);
        }
        $total = $CI->db->get_where('tblpay_slip', array('tblpay_slip.payment_mode' => $id_payment_modes))->row()->payment;

        $CI->db->select_sum('total');
        if (!empty($EndDate)) {
            $CI->db->where('tblother_payslips.date >=', $startDate);
            $CI->db->where('tblother_payslips.date <=', $EndDate);
        } else {
            $CI->db->where('tblother_payslips.date <', $startDate);
        }
        $total_v1 = $CI->db->get_where('tblother_payslips', array('tblother_payslips.payment_modes' => $id_payment_modes))->row()->total;

        $CI->db->select_sum('total');
        if (!empty($EndDate)) {
            $CI->db->where('tbladvance_payment.date >=', $startDate);
            $CI->db->where('tbladvance_payment.date <=', $EndDate);
        } else {
            $CI->db->where('tbladvance_payment.date <', $startDate);
        }
        $paymode_c = $CI->db->get_where('tbladvance_payment', array('tbladvance_payment.paymode_c' => $id_payment_modes))->row()->total;
    } elseif (is_numeric($id_payment_modes)) {
        $CI->db->select_sum('payment');

        $total = $CI->db->get_where('tblpay_slip', array('tblpay_slip.payment_mode' => $id_payment_modes))->row()->payment;

        $CI->db->select_sum('total');

        $total_v1 = $CI->db->get_where('tblother_payslips', array('tblother_payslips.payment_modes' => $id_payment_modes))->row()->total;

        $CI->db->select_sum('total');

        $paymode_c = $CI->db->get_where('tbladvance_payment', array('tbladvance_payment.paymode_c' => $id_payment_modes))->row()->total;
    }

    return ($total + $total_v1 + $paymode_c);
}
function getStart_coupons($id_payment_modes = NULL, $startDate = NULL, $EndDate = NULL)
{
    $CI = &get_instance();
    $total = 0;
    $total_v1 = 0;
    if (is_numeric($id_payment_modes) && !empty($startDate)) {
        $CI->db->select_sum('payment');
        if (!empty($EndDate)) {
            $CI->db->where('tblvouchers_coupon.date_vouchers >=', $startDate);
            $CI->db->where('tblvouchers_coupon.date_vouchers <=', $EndDate);
        } else {
            $CI->db->where('tblvouchers_coupon.date_vouchers <', $startDate);
        }
        $total = $CI->db->get_where('tblvouchers_coupon', array('tblvouchers_coupon.payment_mode' => $id_payment_modes))->row()->payment;

        $CI->db->select_sum('total');
        if (!empty($EndDate)) {
            $CI->db->where('tblother_payslips_coupon.date >=', $startDate);
            $CI->db->where('tblother_payslips_coupon.date <=', $EndDate);
        } else {
            $CI->db->where('tblother_payslips_coupon.date <', $startDate);
        }
        $total_v1 = $CI->db->get_where('tblother_payslips_coupon', array('tblother_payslips_coupon.payment_modes' => $id_payment_modes))->row()->total;

        $CI->db->select_sum('total');
        if (!empty($EndDate)) {
            $CI->db->where('tbladvance_payment.date >=', $startDate);
            $CI->db->where('tbladvance_payment.date <=', $EndDate);
        } else {
            $CI->db->where('tbladvance_payment.date <', $startDate);
        }
        $paymode_n = $CI->db->get_where('tbladvance_payment', array('tbladvance_payment.paymode_n' => $id_payment_modes))->row()->total;
    } elseif (is_numeric($id_payment_modes)) {
        $CI->db->select_sum('payment');
        $total = $CI->db->get_where('tblvouchers_coupon', array('tblvouchers_coupon.payment_mode' => $id_payment_modes))->row()->payment;
        $CI->db->select_sum('total');
        $total_v1 = $CI->db->get_where('tblother_payslips_coupon', array('tblother_payslips_coupon.payment_modes' => $id_payment_modes))->row()->total;
        $CI->db->select_sum('total');
        $paymode_n = $CI->db->get_where('tbladvance_payment', array('tbladvance_payment.paymode_n' => $id_payment_modes))->row()->total;
    }
    return ($total + $total_v1 + $paymode_n);
}

function getStart_payslips_v2($id_payment_modes = NULL, $startDate = NULL, $EndDate = NULL)
{
    $CI = &get_instance();
    $total = 0;
    $total_v1 = 0;
    $paymode_c = 0;
    if (is_numeric($id_payment_modes) && !empty($startDate)) {
        $CI->db->select_sum('payment');
        if (!empty($EndDate)) {
            $CI->db->where('tblpay_slip.day_vouchers >=', $startDate);
            $CI->db->where('tblpay_slip.day_vouchers <=', $EndDate);
        } else {
            $CI->db->where('tblpay_slip.day_vouchers <', $startDate);
        }
        $total = $CI->db->get_where('tblpay_slip', array('tblpay_slip.payment_mode' => $id_payment_modes))->row()->payment;

        $CI->db->select_sum('total');
        if (!empty($EndDate)) {
            $CI->db->where('tblother_payslips.date >=', $startDate);
            $CI->db->where('tblother_payslips.date <=', $EndDate);
        } else {
            $CI->db->where('tblother_payslips.date <', $startDate);
        }
        $total_v1 = $CI->db->get_where('tblother_payslips', array('tblother_payslips.payment_modes' => $id_payment_modes))->row()->total;

        $CI->db->select_sum('total');
        if (!empty($EndDate)) {
            $CI->db->where('tbladvance_payment.date >=', $startDate);
            $CI->db->where('tbladvance_payment.date <=', $EndDate);
        } else {
            $CI->db->where('tbladvance_payment.date <', $startDate);
        }
        $paymode_c = $CI->db->get_where('tbladvance_payment', array('tbladvance_payment.paymode_c' => $id_payment_modes))->row()->total;
    }

    return ($total + $total_v1 + $paymode_c);
}
function getStart_coupons_v2($id_payment_modes = NULL, $startDate = NULL, $EndDate = NULL)
{
    $CI = &get_instance();
    $total = 0;
    $total_v1 = 0;
    $paymode_n = 0;
    if (is_numeric($id_payment_modes) && !empty($startDate)) {
        $CI->db->select_sum('payment');
        if (!empty($EndDate)) {
            $CI->db->where('tblvouchers_coupon.date_vouchers >=', $startDate);
            $CI->db->where('tblvouchers_coupon.date_vouchers <=', $EndDate);
        } else {
            $CI->db->where('tblvouchers_coupon.date_vouchers <', $startDate);
        }
        $total = $CI->db->get_where('tblvouchers_coupon', array('tblvouchers_coupon.payment_mode' => $id_payment_modes))->row()->payment;

        $CI->db->select_sum('total');
        if (!empty($EndDate)) {
            $CI->db->where('tblother_payslips_coupon.date >=', $startDate);
            $CI->db->where('tblother_payslips_coupon.date <=', $EndDate);
        } else {
            $CI->db->where('tblother_payslips_coupon.date <', $startDate);
        }
        $total_v1 = $CI->db->get_where('tblother_payslips_coupon', array('tblother_payslips_coupon.payment_modes' => $id_payment_modes))->row()->total;

        $CI->db->select('COALESCE(SUM(total),0) as total');
        if (!empty($EndDate)) {
            $CI->db->where('tbladvance_payment.date >=', $startDate);
            $CI->db->where('tbladvance_payment.date <=', $EndDate);
        } else {
            $CI->db->where('tbladvance_payment.date <', $startDate);
        }
        $paymode_n = $CI->db->get_where('tbladvance_payment', array('tbladvance_payment.paymode_n' => $id_payment_modes))->row()->total;
    }
    return ($total + $total_v1 + $paymode_n);
}
/**
 * Used in data_tables_init function to fix sorting problems when duedate is null
 * Null should be always last
 * @return array
 */
function process_purchases_order_img($id = '')
{
    $purchases = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
    $string_Row = '<ul class="progressbar_img" style="display: flex;flex-direction: row;justify-content: center;">';
    $string_Row .= '<li class="active_img">';
    $string_Row .= staff_profile_image($purchases->staff_create, array('staff-profile-image-small'), 'small', array(
        'data-toggle' => 'tooltip',
        'data-title' => get_staff_full_name($purchases->staff_create) . ' ' . _l('ch_time') . ' ' . _dt($purchases->date_create)
    ));
    $string_Row .= '</li>';
    $active_1 = '';
    $active_2 = '';
    $data_1 = '';
    $data_2 = '';
    $history_status = explode('|', $purchases->history_status);
    $cancel = '';
    $no_event = '';
    $no_click = '';
    if ($purchases->cancel != 0) {
        $no_event = 'no-drop';
        $no_click = 'none-event';
    }
    if ($purchases->status == 2) {
        $data_1 = explode(',', $history_status[1]);
        $active_1 = 'active';
    }
    if ($purchases->status == 3) {
        $data_1 = explode(',', $history_status[1]);
        $data_2 = explode(',', $history_status[2]);
        $active_1 = 'active';
        $active_2 = 'active';
    }

    if (!empty($data_1)) {
        $string_Row .= '<li class="active_img">';
        $string_Row .= staff_profile_image($data_1[0], array('staff-profile-image-small'), 'small', array(
            'data-toggle' => 'tooltip',
            'data-title' => get_staff_full_name($data_1[0]) . ' ' . _l('ch_time') . ' ' . _dt($data_1[1])
        ));
    } else {
        $string_Row .= '<li class=" ' . $no_event . '">';
        $string_Row .= '<a class="' . $no_click . '"  data-loading-text="" onclick="var_status(' . $purchases->status . ',' . $id . '); return false;">' . staff_profile_image(0, array('staff-profile-image-small'), 'small') . '</a>';
    }

    $string_Row .= '</li>';
    if (!empty($data_2)) {
        $string_Row .= '<li class="active_img">';
        $string_Row .= staff_profile_image($data_2[0], array('staff-profile-image-small'), 'small', array(
            'data-toggle' => 'tooltip',
            'data-title' => get_staff_full_name($data_2[0]) . ' ' . _l('ch_time') . ' ' . _dt($data_2[1])
        ));
    } else {
        $string_Row .= '<li class=" ' . $no_event . '">';
        $string_Row .= '<a class="' . $no_click . '"  data-loading-text="" onclick="var_status(' . $purchases->status . ',' . $id . '); return false;">' . staff_profile_image(0, array('staff-profile-image-small'), 'small') . '</a>';
    }
    $string_Row .= '</li>';
    $import = get_table_where('tblimport', array('id_order' => $id), '', 'row');
    $count_items_import = get_items_import($id);
    $import_s = '';
    $status_import = '';
    if (!empty($import)) {
        $string_Row .= '<li class="active_img">';
        $string_Row .= staff_profile_image($import->staff_create, array('staff-profile-image-small'), 'small', array(
            'data-toggle' => 'tooltip',
            'data-title' => get_staff_full_name($import->staff_create) . ' ' . _l('ch_time') . ' ' . _dt($import->date_create)
        ));
        $string_Row .= '</li>';
    } else {
        $string_Row .= '<li class="">';
        $string_Row .= staff_profile_image(0, array('staff-profile-image-small'), 'small');
        $string_Row .= '</li>';
    }


    if ($purchases->cancel != 0) {
        $datas = explode(',', $purchases->cancel);
        if ($datas[0] == '1foso') {
            $string_Row .= '<li class="cancel">';
            $string_Row .= '<div class="cancel"><img src="' . base_url('uploads/company/' . get_option('favicon')) . '" class="staff-profile-image-small" data-toggle="tooltip" data-title="' . _l('system') . ' ' . _l('ch_time') . ' ' . _dt($datas[1]) . '"></div>';
            $string_Row .= '</li>';
        } else {
            $string_Row .= '<li class="cancel">';
            if (is_admin()) {
                $string_Row .= '<a class="cancel" data-loading-text=""  onclick="no_cancel_status(' . $id . '); return false;">' . staff_profile_image($datas[0], array('staff-profile-image-small'), 'small', array(
                    'data-toggle' => 'tooltip',
                    'data-title' => get_staff_full_name($datas[0]) . ' ' . _l('ch_time') . ' ' . _dt($datas[1])
                )) . '</a>';
            } else {
                $string_Row .= '<a class="cancel">' . staff_profile_image($datas[0], array('staff-profile-image-small'), 'small', array(
                    'data-toggle' => 'tooltip',
                    'data-title' => get_staff_full_name($datas[0]) . ' ' . _l('ch_time') . ' ' . _dt($datas[1])
                )) . '</a>';
            }
            $string_Row .= '</li>';
        }
    } else {
        if ($count_items_import == 0) {
            $no_event_ch = 'class="no-drop none-event"';
        } else {
            $no_event_ch = 'class="' . $no_event . '"';
        }
        $string_Row .= '<li class="">';
        $string_Row .= '<a ' . $no_event_ch . ' data-loading-text=""  onclick="cancel_status(' . $id . '); return false;">' . staff_profile_image(0, array('staff-profile-image-small'), 'small') . '</a>';
        $string_Row .= '</li>';
    }

    $string_Row .= '<div class="clearfix"></div></ul>';
    return $string_Row;
}
function process_purchases_order($id = '')
{

    $data[0] = _l('create');
    $data[1] = _l('proceed');
    $data[2] = _l('accept');
    $data[3] = _l('add_items');
    $data[4] = _l('ch_cancel');
    $string_Row = '<ul class="progressbar" style="display: flex;flex-direction: row;justify-content: center;">';
    $string_Row .= '<li class="active">';
    $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="1" >';
    $string_Row .= mb_convert_case($data[0], MB_CASE_TITLE, "UTF-8");
    $string_Row .= '</a>';
    $string_Row .= '</li>';
    $purchases = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
    $active_1 = '';
    $active_2 = '';
    if ($purchases->status == 2) {
        $active_1 = 'active';
    }
    if ($purchases->status == 3) {
        $active_1 = 'active';
        $active_2 = 'active';
    }
    $string_Row .= '<li class="' . $active_1 . '">';
    $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="1" >';
    $string_Row .= mb_convert_case($data[1], MB_CASE_TITLE, "UTF-8");
    $string_Row .= '</a>';
    $string_Row .= '</li>';
    $string_Row .= '<li class="' . $active_2 . '">';
    $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="2" >';
    $string_Row .= $data[2];
    $string_Row .= '</a>';
    $string_Row .= '</li>';
    $import = get_table_where('tblimport', array('id_order' => $id), '', 'row');
    $count_items_import = get_items_import($id);
    $import_s = '';
    $status_import = '';
    if (!empty($import)) {
        $import_s = 'active';
        if ($count_items_import == 0) {
            $status_import = '<span class="inline-block label label-warning">' . _l('ch_imports_full') . '</span>';
        } else {
            $status_import = '<span class="inline-block label label-warning">' . _l('ch_imports_part') . '</span>';
        }
    }
    $string_Row .= '<li class="' . $import_s . '">';
    $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="3" >';
    $string_Row .= mb_convert_case($data[3], MB_CASE_TITLE, "UTF-8");
    $string_Row .= '</a><br>' . $status_import;
    $string_Row .= '</li>';
    $cancel = '';
    if ($purchases->cancel != 0) {
        $cancel = 'active_ch ';
    }
    $string_Row .= '<li class="' . $cancel . '" >';
    $string_Row .= '    <b><a style="color:red;" class="pointer #ff6f00;"    status-procedure="4" >';
    $string_Row .= mb_convert_case($data[4], MB_CASE_TITLE, "UTF-8");
    $string_Row .= '</a></b>';
    $string_Row .= '</li>';
    $string_Row .= '<div class="clearfix"></div></ul>';
    return $string_Row;
}
function data_tables_init_having_v2($aColumns, $sIndexColumn, $sTable, $join = array(), $where = array(), $additionalSelect = array(), $orderby = '', $groupBy = '', $having = '')
{

    $CI = &get_instance();
    $__post = $CI->input->post();

    /*
     * Paging
     */
    $sLimit = "";
    if ((is_numeric($CI->input->post('start'))) && $CI->input->post('length') != '-1') {
        $sLimit = "LIMIT " . intval($CI->input->post('start')) . ", " . intval($CI->input->post('length'));
    }
    $_aColumns = array();
    foreach ($aColumns as $column) {
        // if found only one dot
        if (substr_count($column, '.') == 1 && strpos($column, ' as ') === false) {
            $_column = explode('.', $column);
            if (isset($_column[1])) {
                if (_startsWith($_column[0], 'tbl')) {
                    $_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);
                    array_push($_aColumns, $_prefix);
                } else {
                    array_push($_aColumns, $column);
                }
            } else {
                array_push($_aColumns, $_column[0]);
            }
        } else {
            array_push($_aColumns, $column);
        }
    }
    /*
     * Ordering
     */
    $sOrder = "";
    if ($CI->input->post('order')) {
        $sOrder = "ORDER BY  ";
        foreach ($CI->input->post('order') as $key => $val) {

            $sOrder .= $aColumns[intval($__post['order'][$key]['column'])];

            $__order_column = $sOrder;
            if (strpos($__order_column, ' as ') !== false) {
                $sOrder = strbefore($__order_column, ' as');
            }
            $_order = strtoupper($__post['order'][$key]['dir']);
            if ($_order == 'ASC') {
                $sOrder .= ' ASC';
            } else {
                $sOrder .= ' DESC';
            }
            $sOrder .= ', ';
        }
        if (trim($sOrder) == "ORDER BY") {
            $sOrder = "";
        }
        if ($sOrder == '' && $orderby != '') {
            $sOrder = $orderby;
        } else {
            $sOrder = substr($sOrder, 0, -2);
        }
    } else {
        $sOrder = $orderby;
    }
    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = "";
    if ((isset($__post['search'])) && $__post['search']['value'] != "") {
        $search_value = $__post['search']['value'];

        $sWhere = "WHERE (";
        for ($i = 0; $i < count($aColumns); $i++) {
            $__search_column = $aColumns[$i];
            if (strpos($__search_column, ' as ') !== false) {
                $__search_column = strbefore($__search_column, ' as');
            }
            if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == "true") {
                $sWhere .= $__search_column . " LIKE '%" . $search_value . "%' OR ";
            }
        }
        if (count($additionalSelect) > 0) {
            foreach ($additionalSelect as $searchAdditionalField) {
                if (strpos($searchAdditionalField, ' as ') !== false) {
                    $searchAdditionalField = strbefore($searchAdditionalField, ' as');
                }

                $sWhere .= $searchAdditionalField . " LIKE '%" . $search_value . "%' OR ";
            }
        }
        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    } else {
    }

    /*
     * SQL queries
     * Get data to display
     */
    $_additionalSelect = '';
    if (count($additionalSelect) > 0) {
        $_additionalSelect = ',' . implode(',', $additionalSelect);
    }
    $where = implode(' ', $where);
    if ($sWhere == '') {
        $where = trim($where);
        if (_startsWith($where, 'AND') || _startsWith($where, 'OR')) {
            if (_startsWith($where, 'OR')) {
                $where = substr($where, 2);
            } else {
                $where = substr($where, 3);
            }
            $where = 'WHERE ' . $where;
        }
    }
    $sQuery = "
    SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $_aColumns)) . " " . $_additionalSelect . "
    FROM   $sTable
    " . implode(' ', $join) . "
    $sWhere
    " . $where . "
    $groupBy
    $having
    $sOrder
    $sLimit
    ";
    // return $sQuery;
    // exit($sQuery);
    $rResult = $CI->db->query($sQuery)->result_array();

    /* Data set length after filtering */
    $sQuery = "
    SELECT FOUND_ROWS()
    ";
    $_query = $CI->db->query($sQuery)->result_array();
    $iFilteredTotal = $_query[0]['FOUND_ROWS()'];
    if (_startsWith($where, 'AND')) {
        $where = 'WHERE ' . substr($where, 3);
    }
    /* Total data set length */
    $sQuery = "
    SELECT COUNT(" . $sTable . '.' . $sIndexColumn . ")
    FROM $sTable " . implode(' ', $join) . ' ' . $where;
    $_query = $CI->db->query($sQuery)->result_array();
    $iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];
    /*
     * Output
     */
    $output = array(
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );

    return array(
        'rResult' => $rResult,
        'output' => $output
    );
}
/**
 * Prefix field name with table ex. table.column
 * @param  string $table
 * @param  string $alias
 * @param  string $field field to check
 * @return string
 */
function test_quantity_order_warehouses($id)
{
    $dem = 0;
    $result_dem = 0;

    $CI = &get_instance();
    $CI->db->select('*,SUM(quantity) as quantitys');
    $CI->db->where('order_id', $id);
    $CI->db->group_by('tbl_order_items.item_id,tbl_order_items.type_item');
    $itemss = $CI->db->get('tbl_order_items')->result_array();
    usort($itemss, ch_make_cmp(['type_item' => "desc", 'item_id' => "asc"]));
    foreach ($itemss as $key => $v) {
        $dem++;
        if ($v['type_item'] == 'products') {
            $v['type_item'] = 'product';
        }
        if ($v['type_item'] == 'materials') {
            $v['type_item'] = 'nvl';
        }
        if ($v['type_item'] == 'tools_supplies') {
            $v['type_item'] = 'tools';
        }
        $CI->db->where('id_items', $v['item_id']);
        if ($v['type_item'] == 'products') {
            $v['type_item'] = 'product';
        }
        $CI->db->where('type_items', $v['type_item']);
        $CI->db->having('SUM(product_quantity) >=', ($v['quantitys'] - $v['quantity_delivery']));
        $result = $CI->db->get('tblwarehouse_items')->row();
        if (!empty($result)) {
            $result_dem++;
        }
    }
    if (empty($result_dem)) {
        $data = false;
    } else {
        if ($result_dem == $dem) {
            $data = true;
        } else {
            $data = false;
        }
    }
    return $data;
}
function test_purchase_import($id)
{
    $order = get_table_where('tblpurchase_order', array('id' => $id), '', 'row');
    $id_purchases = -1;
    if (!empty($order->id_purchase_proce)) {
        $id_purchases = $order->id_purchase_proce;
    } elseif (!empty($order->id_purchases)) {
        $id_purchases = $order->id_purchases;
    }

    $dem = 0;
    $CI = &get_instance();
    $CI->db->where('id_purchase_proce', $id_purchases);
    $CI->db->or_where('id_purchases', $id_purchases);
    $order_all = $CI->db->get('tblpurchase_order')->result_array();
    $id_order = array();
    foreach ($order_all as $key => $value) {
        $id_order[] = $value['id'];
    }
    $purchases = get_table_where('tblpurchases_items', array('purchases_id' => $id_purchases));
    foreach ($purchases as $key => $value) {
        $CI->db->having('SUM(quantity_net) >=', $value['quantity_net']);
        $CI->db->where('product_id', $value['product_id']);
        $CI->db->where('type', $value['type']);
        $CI->db->where_in('id_order', $id_order);
        $CI->db->join('tblimport', 'tblimport.id=tblimport_items.id_import', 'left');
        $CI->db->group_by('product_id,type');
        $ktr = $CI->db->get('tblimport_items')->row();
        if (!empty($ktr)) {
            $dem++;
        }
    }
    if ($dem == count($purchases)) {
        return 1;
    } else
        if ($dem > 0) {
        return 2;
    } else {
        return 3;
    }
}
function get_full_item($id = '', $type = '')
{
    $CI = &get_instance();
    if ($type == 'items') {
        $CI->db->select('tblitems.*,tblunits.unit as unit_name')->distinct();
        $CI->db->from('tblitems');
        $CI->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
        $CI->db->order_by('tblitems.id', 'desc');
        if (is_numeric($id)) {

            $CI->db->where('tblitems.id', $id);
            $item = $CI->db->get()->row();
            $item->color = format_item_color($id, $type);
            $item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/' . $item->avatar) ? base_url('uploads/materials/' . $item->avatar) : (file_exists('uploads/products/' . $item->avatar) ? base_url('uploads/products/' . $item->avatar) : base_url('assets/images/preview-not-available.jpg')))) : base_url('assets/images/preview-not-available.jpg'));
            return $item;
        }
    } else {
        $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
        $CI->db->select($table . '.*,' . $table . '.images as avatar,tblunits.unit as unit_name')->distinct();
        $CI->db->from($table);
        $CI->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
        $CI->db->order_by($table . '.id', 'desc');
        if (is_numeric($id)) {

            $CI->db->where($table . '.id', $id);
            $item = $CI->db->get()->row();
            $item->color = format_item_color($id, $type);
            $item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/' . $item->avatar) ? base_url('uploads/materials/' . $item->avatar) : (file_exists('uploads/products/' . $item->avatar) ? base_url('uploads/products/' . $item->avatar) : (file_exists('uploads/tools_supplies/' . $item->avatar) ? base_url('uploads/tools_supplies/' . $item->avatar) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
            return $item;
        }
    }
}
function sum_from_table_join_v2($table, $attr = array())
{
    // var_dump($attr['where']);die;
    if (!isset($attr['field'])) {
        show_error('sum_from_table(); function expect field to be passed.');
    }
    $CI = &get_instance();
    if (isset($attr['where']) && is_array($attr['where'])) {
        $i = 0;
        foreach ($attr['where'] as $key => $val) {
            if (is_numeric($key)) {
                $CI->db->where($val);
                unset($attr['where'][$key]);
            }
            $i++;
        }
        $CI->db->where($attr['where']);
    }
    if (isset($attr['where_or']) && !empty($attr['where_or'])) {
        $CI->db->where($attr['where_or']);
    }
    if (isset($attr['join']) && is_array($attr['join'])) {
        foreach ($attr['join'] as $key => $val) {
            $val = explode(',', $val);
            if (count($val) == 3) {
                $CI->db->join($val[0], $val[1], $val[2]);
            } else {
                $CI->db->join($val[0], $val[1]);
            }
        }
    } elseif (strlen($attr['join']) > 0) {
        $attr['join'] = explode(',', $attr['join']);
        if (count($attr['join']) == 3) {
            $CI->db->join($attr['join'][0], $attr['join'][1], $attr['join'][1]);
        } else {
            $CI->db->join($attr['join'][0], $attr['join'][1]);
        }
    }
    // $CI->db->select('product_id,product_quantity,warehouse,warehouseid');
    $CI->db->select('COALESCE(SUM(' . $attr['field'] . '),0) as sum');
    $CI->db->from($table);
    $result = $CI->db->get()->row();
    // var_dump($CI->db->last_query());die;
    // echo "<pre>";
    // var_dump($result);die;
    $field = $attr['field'];
    if (strpos($attr['field'], '.') !== false) {
        $field = strafter($attr['field'], '.');
    }
    return $result->sum;
}
function get_debt_client($userid = '')
{
    $clients = get_table_where('tblclients', array('userid' => $userid), '', 'row');
    if (empty($clients)) {
        return 0;
    }
    $whereJoin = array();
    $whereJoin['join'] = array();
    $whereJoin['where'][] = 'tbl_orders.customer_id =' . $userid;
    $whereJoin['field'] = 'grand_total';
    $subtotal = sum_from_table_join('tbl_orders', $whereJoin);

    $whereJoin1 = array();
    $whereJoin1['where'][] = 'tblvouchers_coupon.customer =' . $userid;
    $whereJoin1['join'] = array();
    $whereJoin1['field'] = 'tblvouchers_coupon.payment';
    $total_payment = sum_from_table_join('tblvouchers_coupon', $whereJoin1);

    $whereJoin1 = array();
    $whereJoin1['where'][] = 'tblother_payslips_coupon.objects_id =' . $userid;
    $whereJoin1['where'][] = 'tblother_payslips_coupon.objects = 1';
    $whereJoin1['join'] = array();
    $whereJoin1['field'] = 'tblother_payslips_coupon.total';
    $total_payment_other = sum_from_table_join('tblother_payslips_coupon', $whereJoin1);

    // $whereJoin2 = array();
    // $whereJoin2['where'][] = 'tbl_orders.customer_id =' . $userid;
    // $whereJoin2['join'] = array();
    // $whereJoin2['field'] = 'deductible';
    // $total_payment_invoice = sum_from_table_join('tbl_orders', $whereJoin2);

    $whereJoin3['join'] = array();
    $whereJoin3['where'][] = 'tbl_returned_goods.customer_id =' . $userid;
    $whereJoin3['where'][] = 'tbl_returned_goods.handling_solution = "debt_reduction"';

    $whereJoin3['field'] = 'grand_total';
    $total_return = (sum_from_table_join_v2('tbl_returned_goods', $whereJoin3));


    $debt_client = ($clients->debt_begin + $subtotal - $total_payment - $total_return - $total_payment_other);
    return $debt_client;
}

function debt_clients_date($id_client = null, $start_date_search = null, $end_date_search = null)
{
    $CI = &get_instance();
    if (!empty($start_date_search)) {
        $start_date_search = to_sql_date($start_date_search) . ' 00:00:00';
    }
    if (!empty($end_date_search)) {
        $end_date_search = to_sql_date($end_date_search) . ' 23:59:59';
    }
    $vouchers_coupon_dk = '
    COALESCE(
      (
        SELECT SUM(tblvouchers_coupon.payment) 
        FROM tblvouchers_coupon 
        WHERE tblvouchers_coupon.customer = tblclients.userid ';
    if (!empty($start_date_search)) {
        $vouchers_coupon_dk .= 'AND tblvouchers_coupon.date_vouchers < "' . $start_date_search . '"';
    } else {
        $vouchers_coupon_dk .= 'AND tblvouchers_coupon.id = -1';
    }
    $vouchers_coupon_dk .= '),0)
      
  ';
    $other_dk = '
    COALESCE(
      (
        SELECT SUM(tblother_payslips_coupon.total) 
        FROM tblother_payslips_coupon 
        WHERE tblother_payslips_coupon.objects_id = tblclients.userid AND tblother_payslips_coupon.objects = 1 
        ';
    if (!empty($start_date_search)) {
        $other_dk .= 'AND tblother_payslips_coupon.date < "' . $start_date_search . '"';
    } else {
        $other_dk .= 'AND tblother_payslips_coupon.id = -1';
    }
    $other_dk .= '),0)
      
  ';
    $sumGrandTotal_dk = '(
            SELECT SUM(tbl_deliveries.grand_total)
            FROM tbl_deliveries
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_deliveries.order_id
            WHERE tbl_deliveries.customer_id = tblclients.userid AND tbl_orders.type_orders NOT IN (2, 4, 11)
            ';
    if (!empty($start_date_search)) {
        $sumGrandTotal_dk .= 'AND tbl_deliveries.date < "' . $start_date_search . '"';
    } else {
        $sumGrandTotal_dk .= 'AND tbl_deliveries.id = -1';
    }
    $sumGrandTotal_dk .= ")";


    $return_dk = '
            COALESCE(
                (SELECT SUM(tbl_returned_goods.grand_total) 
                FROM tbl_returned_goods 
                WHERE tbl_returned_goods.handling_solution = "debt_reduction" AND tbl_returned_goods.customer_id = tblclients.userid ';
    if (!empty($start_date_search)) {
        $return_dk .= 'AND tbl_returned_goods.date < "' . $start_date_search . '"';
    } else {
        $return_dk .= 'AND tbl_returned_goods.id = -1';
    }
    $return_dk .= '),0)';


    $vouchers_coupon = '
    COALESCE(
      (
        SELECT COALESCE(SUM(tblvouchers_coupon.payment),0) 
        FROM tblvouchers_coupon 
        LEFT JOIN tblpayment_modes ON tblpayment_modes.id = tblvouchers_coupon.payment_mode
        WHERE tblvouchers_coupon.customer = tblclients.userid ';
    if (!empty($start_date_search)) {
        $vouchers_coupon .= ' AND tblvouchers_coupon.date_vouchers >= "' . $start_date_search . '" ';
    }
    if (!empty($end_date_search)) {
        $vouchers_coupon .= ' AND tblvouchers_coupon.date_vouchers <= "' . $end_date_search . '" ';
    }
    $vouchers_coupon .= ' AND tblpayment_modes.cash = 1 ';
    $vouchers_coupon .= '),0)
      
  ';
    $other = '
    COALESCE(
      (
        SELECT SUM(tblother_payslips_coupon.total) 
        FROM tblother_payslips_coupon 
        LEFT JOIN tblpayment_modes ON tblpayment_modes.id = tblother_payslips_coupon.payment_modes
        WHERE tblother_payslips_coupon.objects_id = tblclients.userid AND tblother_payslips_coupon.objects = 1 
        ';
    if (!empty($start_date_search)) {
        $other .= ' AND tblother_payslips_coupon.date >= "' . $start_date_search . '" ';
    }
    if (!empty($end_date_search)) {
        $other .= ' AND tblother_payslips_coupon.date <= "' . $end_date_search . '" ';
    }
    $other .= ' AND tblpayment_modes.cash = 1 ';

    $other .= '),0)';

    $vouchers_coupon_bank = '
    COALESCE(
      (
        SELECT COALESCE(SUM(tblvouchers_coupon.payment),0) 
        FROM tblvouchers_coupon 
        LEFT JOIN tblpayment_modes ON tblpayment_modes.id = tblvouchers_coupon.payment_mode
        WHERE tblvouchers_coupon.customer = tblclients.userid ';
    if (!empty($start_date_search)) {
        $vouchers_coupon_bank .= 'AND tblvouchers_coupon.date_vouchers >= "' . $start_date_search . '"';
    }
    if (!empty($end_date_search)) {
        $vouchers_coupon_bank .= 'AND tblvouchers_coupon.date_vouchers <= "' . $end_date_search . '"';
    }
    $vouchers_coupon_bank .= 'AND tblpayment_modes.bank = 1';
    $vouchers_coupon_bank .= '),0)
      
  ';
    $other_bank = '
    COALESCE(
      (
        SELECT SUM(tblother_payslips_coupon.total) 
        FROM tblother_payslips_coupon 
        LEFT JOIN tblpayment_modes ON tblpayment_modes.id = tblother_payslips_coupon.payment_modes
        WHERE tblother_payslips_coupon.objects_id = tblclients.userid AND tblother_payslips_coupon.objects = 1 
        ';
    if (!empty($start_date_search)) {
        $other_bank .= 'AND tblother_payslips_coupon.date >= "' . $start_date_search . '"';
    }
    if (!empty($end_date_search)) {
        $other_bank .= 'AND tblother_payslips_coupon.date <= "' . $end_date_search . '"';
    }
    $other_bank .= 'AND tblpayment_modes.bank = 1';

    $other_bank .= '),0)';

    $sumGrandTotal = '(
            SELECT SUM(tbl_deliveries.grand_total)
            FROM tbl_deliveries 
            INNER JOIN tbl_orders ON tbl_orders.id = tbl_deliveries.order_id
            WHERE tbl_deliveries.customer_id = tblclients.userid AND tbl_orders.type_orders NOT IN (2, 4, 11)
            ';
    if (!empty($start_date_search)) {
        $sumGrandTotal .= 'AND tbl_deliveries.date >= "' . $start_date_search . '"';
    }
    if (!empty($end_date_search)) {
        $sumGrandTotal .= 'AND tbl_deliveries.date <= "' . $end_date_search . '"';
    }
    $sumGrandTotal .= ")";

    $sumGrandTotal_tax = '(
        SELECT SUM(tbl_deliveries.total_tax)
        FROM tbl_deliveries 
        INNER JOIN tbl_orders ON tbl_orders.id = tbl_deliveries.order_id
        WHERE tbl_deliveries.customer_id = tblclients.userid AND tbl_orders.type_orders NOT IN (2, 4, 11)
        ';
    if (!empty($start_date_search)) {
        $sumGrandTotal_tax .= 'AND tbl_deliveries.date >= "' . $start_date_search . '"';
    }
    if (!empty($end_date_search)) {
        $sumGrandTotal_tax .= 'AND tbl_deliveries.date <= "' . $end_date_search . '"';
    }
    $sumGrandTotal_tax .= ")";

    $return = '
            COALESCE(
                (SELECT SUM(tbl_returned_goods.grand_total) 
                FROM tbl_returned_goods 
                WHERE tbl_returned_goods.handling_solution = "debt_reduction" AND tbl_returned_goods.customer_id = tblclients.userid ';
    if (!empty($start_date_search)) {
        $return .= 'AND tbl_returned_goods.date >= "' . $start_date_search . '"';
    }
    if (!empty($end_date_search)) {
        $return .= 'AND tbl_returned_goods.date <= "' . $end_date_search . '"';
    }
    $return .= '),0)
             as returns';
    $text = 'COALESCE(' . ($sumGrandTotal_dk . ' - ' . $vouchers_coupon_dk . ' - ' . $other_dk . ' - ' . $return_dk . ' ') . ',0) + tblclients.debt_begin as begin,(COALESCE(' . $sumGrandTotal . ',0)) as total_import,(COALESCE(' . $sumGrandTotal_tax . ',0)) as total_tax ,' . $return . ',(' . $vouchers_coupon . ' + ' . $other . ')  as total_payment_import,(' . $vouchers_coupon_bank . ' + ' . $other_bank . ')  as total_payment_import_bank';
    $CI->db->select($text);
    $CI->db->where("userid", $id_client);
    $total = $CI->db->get("tblclients")->row_array();
    return $total;
}

function get_pods($text)
{
    $html = '';
    if (!empty($text)) {
        $array = explode(',', $text);
        foreach ($array as $key => $value) {
            $ktr = get_table_where('tbl_productions_orders_details', array('id' => $value), '', 'row');
            if (!empty($ktr)) {
                $html .= $ktr->reference_no . ', ';
            }
        }
    }

    return trim($html, ', ');
}
function get_pod_news($text)
{
    $html = '';
    if (!empty($text)) {
        $array = explode(',', $text);
        foreach ($array as $key => $value) {
            $ktr = get_table_where('tbl_productions_orders_details', array('id' => $value), '', 'row');
            if (!empty($ktr)) {
                $html .= $ktr->reference_no . '<br>';
            }
        }
    }

    return trim($html, '<br>');
}

function get_orders($text)
{
    $html = '';
    if (!empty($text)) {
        $array = explode(',', $text);
        foreach ($array as $key => $value) {
            $ktr = get_table_where('tbl_orders', array('id' => $value), '', 'row');
            if (!empty($ktr)) {
                $html .= $ktr->reference_no . ', ';
            }
        }
    }

    return trim($html, ', ');
}
function get_orders_news($text)
{
    $html = '';
    if (!empty($text)) {
        $array = explode(',', $text);
        foreach ($array as $key => $value) {
            $ktr = get_table_where('tbl_orders', array('id' => $value), '', 'row');
            if (!empty($ktr)) {
                $html .= $ktr->reference_no . '<br>';
            }
        }
    }

    return trim($html, '<br>');
}
function get_plans($text)
{
    $html = '';
    if (!empty($text)) {
        $array = explode(',', $text);
        foreach ($array as $key => $value) {
            $ktr = get_table_where('tbl_business_plan', array('id' => $value), '', 'row');
            if (!empty($ktr)) {
                $html .= $ktr->reference_no . ', ';
            }
        }
    }

    return trim($html, ', ');
}
function get_plan_news($text)
{
    $html = '';
    if (!empty($text)) {
        $array = explode(',', $text);
        foreach ($array as $key => $value) {
            $ktr = get_table_where('tbl_business_plan', array('id' => $value), '', 'row');
            if (!empty($ktr)) {
                $html .= $ktr->reference_no . '<br>';
            }
        }
    }

    return trim($html, '<br>');
}

function count_po($text)
{
    $ci = get_instance();
    $count = 0;
    if (!empty($text)) {
        $array = explode(',', $text);
        $ci->db->select('tbl_productions_orders_details.productions_orders_id as productions_orders_id');
        $ci->db->where_in('id', $array);
        $pod = $ci->db->get('tbl_productions_orders_details')->result_array();
        if (!empty($pod)) {
            $po_id = 0;
            foreach ($pod as $key => $value) {
                if ($po_id != $value['productions_orders_id']) {
                    $count++;
                    $po_id = $value['productions_orders_id'];
                }
            }
        }
    }

    return $count;
}

function get_po($text)
{
    $ci = get_instance();
    $html = '';
    if (!empty($text)) {
        $array = explode(',', $text);
        $ci->db->select('tbl_productions_orders_details.productions_orders_id as productions_orders_id');
        $ci->db->where_in('id', $array);
        $pod = $ci->db->get('tbl_productions_orders_details')->result_array();
        if (!empty($pod)) {
            $po_id = 0;
            foreach ($pod as $key => $value) {
                if ($po_id != $value['productions_orders_id']) {
                    $ktr = get_table_where('tbl_productions_orders', array('id' => $value['productions_orders_id']), '', 'row');
                    if (!empty($ktr)) {
                        $html .= $ktr->reference_no . '<br>';
                    }
                    $po_id = $value['productions_orders_id'];
                }
            }
        }
    }

    return trim($html, '<br>');
}
function get_po_new($text)
{
    $ci = get_instance();
    $html = '';
    if (!empty($text)) {
        $array = explode(',', $text);
        $ci->db->select('tbl_productions_orders_details.productions_orders_id as productions_orders_id');
        $ci->db->where_in('id', $array);
        $pod = $ci->db->get('tbl_productions_orders_details')->result_array();
        if (!empty($pod)) {
            $po_id = 0;
            foreach ($pod as $key => $value) {
                if ($po_id != $value['productions_orders_id']) {
                    $ktr = get_table_where('tbl_productions_orders', array('id' => $value['productions_orders_id']), '', 'row');
                    if (!empty($ktr)) {
                        $html .= $ktr->reference_no . ', ';
                    }
                    $po_id = $value['productions_orders_id'];
                }
            }
        }
    }

    return trim($html, ', ');
}
function get_po_id($text)
{
    $ci = get_instance();
    $html = '';
    if (!empty($text)) {
        $array = explode(',', $text);
        $ci->db->select('tbl_productions_orders_details.productions_orders_id as productions_orders_id');
        $ci->db->where_in('id', $array);
        $pod = $ci->db->get('tbl_productions_orders_details')->result_array();
        if (!empty($pod)) {
            $po_id = 0;
            foreach ($pod as $key => $value) {
                if ($po_id != $value['productions_orders_id']) {
                    $ktr = get_table_where('tbl_productions_orders', array('id' => $value['productions_orders_id']), '', 'row');
                    if (!empty($ktr)) {
                        $html .= $ktr->id . ', ';
                    }
                    $po_id = $value['productions_orders_id'];
                }
            }
        }
    }

    return trim($html, ', ');
}
function rand_color()
{
    return substr('00000' . dechex(mt_rand(0, 0xffffff)), -6);
}
function process_qc($id = '')
{
    $create = _l('create');
    $data[0] = _l('Giám đốc phân xưởng');
    $data[1] = _l('Phòng QC');
    $string_Row = '<ul class="progressbar" style="display: flex;flex-direction: row;justify-content: center;">';
    $string_Row .= '<li class="active">';
    $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="1" >';
    $string_Row .= mb_convert_case($create, MB_CASE_TITLE, "UTF-8");
    $string_Row .= '</a>';
    $string_Row .= '</li>';
    $check_quality = get_table_where('tbl_check_quality', array('id' => $id), '', 'row');
    $active_1 = '';
    $active_2 = '';
    $active_0 = '';
    if ($check_quality->status_process == 1) {
        $active_0 = 'active';
    }
    if ($check_quality->status_process == 2) {
        $active_0 = 'active';
        $active_1 = 'active';
    }
    $string_Row .= '<li class="' . $active_0 . '">';
    $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="1" >';
    $string_Row .= $data[0];
    $string_Row .= '</a>';
    $string_Row .= '</li>';

    $string_Row .= '<li class="' . $active_1 . '">';
    $string_Row .= '    <a class="pointer #ff6f00"    status-procedure="2" >';
    $string_Row .= mb_convert_case($data[1], MB_CASE_TITLE, "UTF-8");
    $string_Row .= '</a>';
    $string_Row .= '</li>';

    return $string_Row;
}

function process_qc_img($id = '')
{
    $check_quality = get_table_where('tbl_check_quality', array('id' => $id), '', 'row');
    $string_Row = '<ul class="progressbar_img" style="display: flex;flex-direction: row;justify-content: center;margin-bottom: 0">';
    $string_Row .= '<li class="active_img">';
    $string_Row .= staff_profile_image(
        $check_quality->created_by,
        array('staff-profile-image-small'),
        'small',
        array(
            'data-toggle' => 'tooltip',
            'data-title' => get_staff_full_name($check_quality->created_by) . ' ' . _l('ch_time') . ' ' . _dt($check_quality->date_created),
        )
    );
    $string_Row .= '</li>';
    $active_0 = '';
    $active_1 = '';
    $active_2 = '';
    $data_0 = '';
    $data_1 = '';
    $data_2 = '';
    $history_status = explode('|', $check_quality->history_status);
    $cancel = '';
    $no_event = '';
    $no_click = '';
    if ($check_quality->status_process == 1) {
        $data_0 = explode(',', $history_status[0]);
        $active_0 = 'active';
    }
    if ($check_quality->status_process == 2) {
        $data_0 = explode(',', $history_status[0]);
        $data_1 = explode(',', $history_status[1]);
        $active_1 = 'active';
    }
    if (!empty($data_0)) {
        $string_Row .= '<li class="active_img">';
        $string_Row .= staff_profile_image($data_0[0], array('staff-profile-image-small'), 'small', array(
            'data-toggle' => 'tooltip',
            'data-title' => get_staff_full_name($data_0[0]) . ' ' . _l('ch_time') . ' ' . _dt($data_0[1]),
        ));
    } else {
        $string_Row .= '<li class=" ' . $no_event . '">';
        $string_Row .= '<a class="' . $no_click . '"  data-loading-text="" onclick="var_status_qc(0,' . $id . '); return false;">' . staff_profile_image(
            0,
            array('staff-profile-image-small'),
            'small'
        ) . '</a>';
    }


    if (!empty($data_1)) {
        $string_Row .= '<li class="active_img">';
        $string_Row .= staff_profile_image($data_1[0], array('staff-profile-image-small'), 'small', array(
            'data-toggle' => 'tooltip',
            'data-title' => get_staff_full_name($data_1[0]) . ' ' . _l('ch_time') . ' ' . _dt($data_1[1]),
        ));
    } else {
        $string_Row .= '<li class=" ' . $no_event . '">';
        $string_Row .= '<a class="' . $no_click . '"  data-loading-text="" onclick="var_status_qc(1,' . $id . '); return false;">' . staff_profile_image(
            0,
            array('staff-profile-image-small'),
            'small'
        ) . '</a>';
    }
    $string_Row .= '</li>';


    $string_Row .= '<div class="clearfix"></div></ul>';

    return $string_Row;
}
function checkTokenLoginApp($tokenAccount = "")
{
    $CI = &get_instance();
    $CI->load->library('encryption');
    $tokenAccount = str_replace("---", "/", base64_decode($tokenAccount));

    return base64_decode($CI->encryption->decrypt($tokenAccount));
}
function staff_profile_image_ch($id = '')
{
    $staff = get_table_where('tblstaff', array('staffid' => $id), '', 'row');
    $url = base_url('assets/images/user-placeholder.jpg');
    if ($staff && $staff->profile_image !== null) {
        $profile_image = base_url('uploads/staff_profile_images/' . $staff->staffid . '/small_' . $staff->profile_image);
    } else {
        $profile_image = $url;
    }
    return $profile_image;
}
function getItemsProductMaterial($value, &$arrItems = array(), &$array_materials = array(), &$array_product_semi = array())
{
    $ci = get_instance();
    $ci->load->model('products_model');
    $ci->load->model('items_model');
    if ($value['type_item'] != 'materials') {
        $arr = explode('__', $value['item_id']);
        $product_id = $arr[0];
        $product = get_table_where('tbl_products', ['id' => $product_id], '', 'row_array', '', 'id, versions');
        $versions = $product['versions'];
        $quantity = $value['quantity'];
        if (!empty($versions)) {
            $version = $ci->products_model->getBomByProductIdAndVersions($product_id, $versions);
            if (!empty($version)) {
                $elements = $ci->products_model->getVersionsElementByVersionId($version['id']);
                if (!empty($elements)) {
                    foreach ($elements as $k => $val) {
                        $quantity_element = $val['quantity'];
                        $total_quantity_element = $quantity * $quantity_element;

                        $element_items = $ci->products_model->getElementItemsByElementId($val['id']);
                        if (!empty($element_items)) {
                            foreach ($element_items as $i => $el) {
                                $quantity_single = $el['quantity'];
                                $total_quantity_item = $total_quantity_element * $quantity_single;
                                $quantity_primary = 0;
                                if ($el['type'] == "semi_products" || $el['type'] == "semi_products_outside") {
                                    $info = $ci->products_model->rowProduct($el['item_id']);
                                    $unit_parent_id = $info['unit_id'];
                                    $quantity_exchange = 1;
                                    $quantity_primary = $total_quantity_item;
                                } else {
                                    $info = $ci->items_model->rowMaterial($el['item_id']);
                                    $unit_id = $el['unit_id'];
                                    $unit_parent_id = $info['unit_id'];
                                    $row_exchange = $ci->products_model->rowExchangeItems($el['item_id'], $unit_id);
                                    $quantity_exchange = 1;
                                    if (!empty($row_exchange)) {
                                        $quantity_exchange = $row_exchange['number_exchange'];
                                    }
                                    if ($quantity_exchange != 0) {
                                        $quantity_primary = $total_quantity_item / $quantity_exchange;
                                    }
                                }
                                $item_id_key = $el['item_id'] . '_' . $el['type'];
                                if (!empty($arrItems[$item_id_key])) {
                                    $arrItems[$item_id_key]['quantity'] = $arrItems[$item_id_key]['quantity'] + $quantity_primary;
                                } else {
                                    $arrItems[$item_id_key] = array(
                                        'item_id' => $el['item_id'] . '__' . $el['type'],
                                        'type_item' => $el['type'],
                                        'quantity' => $quantity_primary
                                    );
                                    if ($el['type'] == 'materials') {
                                        $array_materials[] = $el['item_id'];
                                    } elseif ($el['type'] == "semi_products" || $el['type'] == "semi_products_outside") {
                                        $array_product_semi[] = $el['item_id'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $arrItems;
}
function checkWarningWarehouse()
{
    $ci = &get_instance();
    $quantityInventory = "(
        SELECT
            SUM(tblwarehouse_items.product_quantity)
        FROM tblwarehouse_items
        INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
        WHERE tblwarehouse_items.id_items = tbl_materials.id AND tblwarehouse_items.type_items = 'nvl' AND tblwarehouse.supplier_id = 0 AND tblwarehouse.id != 8
    )";
    $quantityInventorySemi = "(
        SELECT
            SUM(tblwarehouse_items.product_quantity)
        FROM tblwarehouse_items
        INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
        INNER JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion
        WHERE tblwarehouse_items.id_items = tbl_products.id AND tblwarehouse_items.type_items = 'product' AND tblwarehouse.supplier_id = 0
        AND IF(tbl_products.type_products = 'semi_products_outside', tblwarehouse.id != 8, tbllocaltion_warehouses.pod_id = 0)
    )";
    $quantityInventoryTool = "(
        SELECT
            SUM(tblwarehouse_items.product_quantity)
        FROM tblwarehouse_items
        INNER JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_items.warehouse_id
        WHERE tblwarehouse_items.id_items = tbl_tools_supplies.id AND tblwarehouse_items.type_items = 'tools' AND tblwarehouse.supplier_id = 0 AND tblwarehouse.id != 8
    )";
    $items = [];
    $array_materials = [0];
    $array_product_semi = [0];
    $ci->db->select('tbl_order_items.*');
    $ci->db->from('tbl_orders');
    $ci->db->join('tbl_order_items', 'tbl_order_items.order_id = tbl_orders.id', 'left');
    $ci->db->where('(tbl_order_items.quantity - tbl_order_items.quantity_plan) > ', 0);
    $orders = $ci->db->get()->result_array();
    if (!empty($orders)) {
        foreach ($orders as $key => $value) {
            getItemsProductMaterial($value, $items, $array_materials, $array_product_semi);
        }
    }

    if (!empty($items)) {
        foreach ($items as $key => $value) {
            getItemsProductMaterial($value, $items, $array_materials, $array_product_semi);
        }
    }

    $arr_materials = implode(",", $array_materials);
    $arr_product_semi = implode(",", $array_product_semi);
    $query = "
        
            SELECT 
                tbl_materials.id as id,
                CONCAT('materials') as item_type,
                tbl_materials.code as item_code,
                tbl_materials.name as item_name,
                tbl_materials.images as images,
                tblunits.unit as unit_name,
                0 as quantity_bom,
                tbl_materials.quantity_minimum as quantity,
                COALESCE($quantityInventory, 0) as quantity_inventory,
                0 as quantity_purchase

            FROM tbl_materials 
            LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_id
            WHERE tbl_materials.id  IN  ($arr_materials)
        
        UNION ALL
        
            SELECT
                tbl_products.id as id,
                tbl_products.type_products as item_type,
                tbl_products.code as item_code,
                tbl_products.name as item_name,
                tbl_products.images as images,
                tblunits.unit as unit_name,
                0 as quantity_bom,
                tbl_products.quantity_minimum as quantity,
                COALESCE($quantityInventorySemi, 0) as quantity_inventory,
                0 as quantity_purchase
            FROM tbl_products 
            LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id
            WHERE tbl_products.id  IN  ($arr_product_semi)
        
        UNION ALL
        
            SELECT
                tbl_tools_supplies.id as id,
                CONCAT('tools_supplies') as item_type,
                tbl_tools_supplies.code as item_code,
                tbl_tools_supplies.name as item_name,
                tbl_tools_supplies.images as images,
                tblunits.unit as unit_name,
                0 as quantity_bom,
                tbl_tools_supplies.quantity_minimum as quantity,
                COALESCE($quantityInventoryTool, 0) as quantity_inventory,
                0 as quantity_purchase

            FROM tbl_tools_supplies 
            LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id
            WHERE tbl_tools_supplies.quantity_minimum > 0
        
    ";
    $result = $ci->db->query($query)->result_array();
    $check = false;
    if (!empty($result)) {
        foreach ($result as $key => $value) {
            $quantity = $value['quantity_bom'];
            if (array_search($value['id'] . '__' . $value['item_type'], array_column($items, 'item_id')) !== false) {
                $quantity = array_column($items, 'quantity', 'item_id')[$value['id'] . '__' . $value['item_type']];
            }
            if (($quantity + $value['quantity']) > $value['quantity_inventory']) {
                $check = true;
                break;
            }
        }
    }
    return $check;
}

function get_items_import_outsource($id)
{
    $dem = 0;
    $CI = &get_instance();
    $main = get_table_where('tbl_import_outsource', ['id' => $id], '', 'row_array');
    $warehouse = $main['warehouse_to'];
    $CI->db->select('*,SUM(quantity) as quantity');
    $CI->db->where('import_outsource_id', $id);
    $CI->db->group_by('tbl_import_outsource_items.item_id,tbl_import_outsource_items.locaiton_to,tbl_import_outsource_items.type_item');
    $itemss = $CI->db->get('tbl_import_outsource_items')->result_array();
    usort($itemss, ch_make_cmp(['type_item' => "desc", 'item_id' => "asc"]));
    foreach ($itemss as $key => $v) {
        if ($v['type_item'] == 'products') {
            $v['type_item'] = 'product';
        } elseif ($v['type_item'] == 'materials') {
            $v['type_item'] = 'nvl';
        } elseif ($v['type_item'] == 'tools_supplies') {
            $v['type_item'] = 'tools';
        } elseif ($v['type_item'] == 'semi_products') {
            $v['type_item'] = 'product';
        } elseif ($v['type_item'] == 'semi_products_outside') {
            $v['type_item'] = 'product';
        }
        $CI->db->or_group_start();
        $CI->db->where('id_items', $v['item_id']);
        $CI->db->where('type_items', $v['type_item']);
        $CI->db->where('localtion', $v['locaiton_to']);
        $CI->db->where('warehouse_id', $warehouse);
        $CI->db->group_end();
    }
    $result = $CI->db->get('tblwarehouse_items')->result_array();
    foreach ($itemss as $k => $v) {
        $ktr = 0;
        if ($v['type_item'] == 'products') {
            $v['type_item'] = 'product';
        } elseif ($v['type_item'] == 'materials') {
            $v['type_item'] = 'nvl';
        } elseif ($v['type_item'] == 'tools_supplies') {
            $v['type_item'] = 'tools';
        } elseif ($v['type_item'] == 'semi_products') {
            $v['type_item'] = 'product';
        } elseif ($v['type_item'] == 'semi_products_outside') {
            $v['type_item'] = 'product';
        }
        $itemss[$k]['name_ware'] = get_table_where('tblwarehouse', array('id' => $warehouse), '', 'row')->name;
        $itemss[$k]['name_location'] = get_table_where('tbllocaltion_warehouses', array('id' => $v['locaiton_to']), '', 'row')->name;
        $itemss[$k]['image'] = get_items($v['item_id'], $v['type_item'])->avatar_1;
        foreach ($result as $key => $value) {
            if (($v['item_id'] == $value['id_items']) && ($warehouse == $value['warehouse_id']) && ($v['locaiton_to'] == $value['localtion']) && ($v['type_item'] == $value['type_items'])) {
                $ktr = 1;
                $itemss[$k]['type'] = format_item_purchases($value['type_items']);
                $itemss[$k]['quantity_net'] = $v['quantity'] - $value['product_quantity'];
            }
        }
        if ($ktr == 0) {
            $itemss[$k]['type'] = format_item_purchases($v['type_item']);
            $itemss[$k]['quantity_net'] = $v['quantity'];
        }

        if ($itemss[$k]['quantity_net'] <= 0) {
            unset($itemss[$k]);
        }
    }
    return $itemss;
}
function sumExistsQ_all_viewinventory($product_id = '', $items = array(), $index = 0)
{
    $total = 0;
    if (is_numeric($product_id) && isset($items)) {
        for ($i = $index; $i < count($items); $i++) {
            $row = (object) $items[$i];
            if (($row->product_id == $product_id)) {
                $total += $row->check_quantity;
            }
            if (($row->product_id != $product_id))
                break;
        }
    }
    return $total;
}
function ssumExistsQ_all_viewinventory_v2($product_id = '', $items = array(), $index = 0, $type = '')
{
    $total = 0;
    if (is_numeric($product_id) && isset($items) && isset($type)) {
        for ($i = $index; $i < count($items); $i++) {
            $row = (object) $items[$i];
            if (($row->product_id == $product_id) && ($row->type == $type)) {
                $total += $row->check_quantity;
            }
            if (($row->product_id != $product_id) && ($row->type != $type))
                break;
        }
    }

    return $total;
}
function sumExistsQ_viewinventory($localtion, $product_id, $items = array(), $index = 0, $type = '')
{
    $total = 0;
    if (is_numeric($product_id) && isset($items)) {
        for ($i = $index; $i < count($items); $i++) {
            $row = (object) $items[$i];
            if (($row->product_id == $product_id) && ($row->localtion_id == $localtion) && ($row->type == $type)) {
                $total += $row->check_quantity;
            }
            if (($row->product_id != $product_id) && ($row->localtion_id == $localtion) && ($row->type == $type))
                break;
        }
    }
    return $total;
}
function getTagest($id_warehouse = 0, $type = '')
{
    $CI = &get_instance();
    $CI->db->select('tbl_stages.*');
    $CI->db->join('tbllocaltion_warehouses', 'tbllocaltion_warehouses.stage_id=tbl_stages.id', 'left');
    $CI->db->join('tblwarehouse_items', 'tblwarehouse_items.localtion=tbllocaltion_warehouses.id', 'left');
    if ($type == 'product' || $type == 'semi_products') {
        $CI->db->join('tbl_products', 'tbl_products.id = tblwarehouse_items.id_items', 'left');
        if ($type == 'product') {
            $CI->db->group_start();
            $CI->db->where('tbl_products.type_products', 'products');
            $CI->db->or_where('tbl_products.type_products', 'semi_products_outside');
            $CI->db->group_end();
        } else {
            $CI->db->where('tbl_products.type_products', 'semi_products');
        }
    }
    $CI->db->where('tblwarehouse_items.warehouse_id', $id_warehouse);
    $CI->db->where('tblwarehouse_items.product_quantity >', 0);
    $CI->db->where('tblwarehouse_items.type_items', 'product');
    $CI->db->group_by('tbl_stages.id');
    $stages = $CI->db->get('tbl_stages')->result_array();
    return $stages;
}
function dt_EditColumSelectInput_pricesupplier(
    $value = "",
    $id = '',
    $name = "",
    $ValShow = "",
    $urlGetData = '',
    $urlFrom = '',
    $indexAddfrom = '',
    $name_data_input = 'data_input'
) {
    $html = '<div class="lableScript">' . $ValShow . ' 
                <a class="editDataTable_ch" data-type="select" data-href="' . $urlGetData . '" data-id="' . $id . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
            </div>
            <div style="width:100%" class="inputScript hide">
                ' . form_open($urlFrom, $indexAddfrom) . '
                    <input style="width:100%" onkeyup="formatNumBerKeyUpCus(this)" name="' . $name_data_input . '" data-hidden="' . $value . '" class="H_input align_right ChangeDataTable" value="' . $value . '"/>
                    <input style="width:100%" name="name_input"  type="hidden" value="' . $name . '"/>
                    <input style="width:100%" name="id" id="id_ch" type="hidden" value="' . $id . '"/>
                    <div style="width:100%" class="clearfix mtop10"></div>
                    <button type="submit" class="btn btn-icon"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                    <button type="button" class="btn btn-icon text-danger closeEditData"><i class="fa fa-times" aria-hidden="true"></i></i></button>
                ' . form_close() . '
            </div>';

    return $html;
}
function dt_EditColumSelectInput_pricesupplier_Four(
    $value = "",
    $id = '',
    $name = "",
    $ValShow = "",
    $urlGetData = '',
    $urlFrom = '',
    $indexAddfrom = '',
    $name_data_input = 'data_input'
) {
    $html = '<div class="lableScript">' . $ValShow . ' 
                <a class="editDataTable_ch" data-type="select" data-href="' . $urlGetData . '" data-id="' . $id . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
            </div>
            <div style="width:100%" class="inputScript hide">
                ' . form_open($urlFrom, $indexAddfrom) . '
                    <input style="width:100%" onkeyup="formatNumBerKeyUpCusFour(this)" name="' . $name_data_input . '" data-hidden="' . $value . '" class="H_input align_right ChangeDataTable" value="' . $value . '"/>
                    <input style="width:100%" name="name_input"  type="hidden" value="' . $name . '"/>
                    <input style="width:100%" name="id" id="id_ch" type="hidden" value="' . $id . '"/>
                    <div style="width:100%" class="clearfix mtop10"></div>
                    <button type="submit" class="btn btn-icon"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                    <button type="button" class="btn btn-icon text-danger closeEditData"><i class="fa fa-times" aria-hidden="true"></i></i></button>
                ' . form_close() . '
            </div>';

    return $html;
}
function dt_get_items($id = '', $type = '')
{
    if ($type == 'products') {
        $type = 'product';
    }
    if ($type == 'materials') {
        $type = 'nvl';
    }
    if ($type == 'tools_supplies') {
        $type = 'tools';
    }
    $CI = &get_instance();
    $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
    if ($type == 'items') {
        $CI->db->select('tblitems.*,tblunits.unit as unit_name,tblitems.price as price');
        $CI->db->from('tblitems');
        $CI->db->join('tblunits', 'tblunits.unitid=tblitems.unit', 'left');
        $CI->db->where('tblitems.id', $id);
        $item = $CI->db->get()->row();
    } elseif ($type == 'tools') {
        $CI->db->select('tbl_tools_supplies.*,tbl_tools_supplies.images as avatar,tblunits.unit as unit_name,tbl_tools_supplies.price_sell  as price');
        $CI->db->from('tbl_tools_supplies');
        $CI->db->join('tblunits', 'tblunits.unitid=tbl_tools_supplies.unit_id', 'left');
        $CI->db->where('tbl_tools_supplies.id', $id);
        $item = $CI->db->get()->row();
    } else {
        $CI->db->select($table . '.*,tblunits.unit as unit_name,' . $table . '.price_sell  as price,' . $table . '.images as avatar');
        $CI->db->from($table);
        $CI->db->join('tblunits', 'tblunits.unitid=' . $table . '.unit_id', 'left');
        $CI->db->where($table . '.id', $id);
        $item = $CI->db->get()->row();
    }
    if (!empty($item)) {
        $item->avatar_2 = (!empty($item->avatar) ? (file_exists($item->avatar) ? ($item->avatar) : (file_exists('uploads/materials/' . $item->avatar) ? ('uploads/materials/' . $item->avatar) : (file_exists('uploads/products/' . $item->avatar) ? ('uploads/products/' . $item->avatar) : (file_exists('uploads/tools_supplies/' . $item->avatar) ? ('uploads/tools_supplies/' . $item->avatar) : '')))) : '');
        $item->avatar_1 = (!empty($item->avatar) ? (file_exists($item->avatar) ? base_url($item->avatar) : (file_exists('uploads/materials/' . $item->avatar) ? base_url('uploads/materials/' . $item->avatar) : (file_exists('uploads/products/' . $item->avatar) ? base_url('uploads/products/' . $item->avatar) : (file_exists('uploads/tools_supplies/' . $item->avatar) ? base_url('uploads/tools_supplies/' . $item->avatar) : base_url('assets/images/preview-not-available.jpg'))))) : base_url('assets/images/preview-not-available.jpg'));
    }

    return $item;
}
function Getinfocode($type = 'code_pck')
{
    $CI = &get_instance();

    $code = get_table_where('tbl_code_info', array('id' => 1), '', 'row');

    if ($type == 'code_pck') {
        $code_pck = sprintf('%06d', $code->code_pck + 1);
        $check_code = get_table_where('tblother_payslips', array('code' => $code_pck, 'is_advance' => 0), '', 'row');
        if (!empty($check_code)) {
            $CI->db->update('tbl_code_info', array('code_pck' => ($code->code_pck + 1)), array('id' => 1));
            return Getinfocode('code_pck');
        } else {
            return $code_pck;
        }
    } elseif ($type == 'code_tu') {
        $code_tu = sprintf('%06d', $code->code_tu + 1);
        $check_code = get_table_where('tblother_payslips', array('code' => $code_tu, 'is_advance' => 1), '', 'row');
        if (!empty($check_code)) {
            $CI->db->update('tbl_code_info', array('code_tu' => ($code->code_tu + 1)), array('id' => 1));
            return Getinfocode('code_tu');
        } else {
            return $code_tu;
        }
    }
    return '';
}

function recursiveCategoryStages($category_stages = null)
{
    $CI = &get_instance();

    $CI->db->select('*');
    $CI->db->where('is_in', 1);
    $CI->db->from('tbl_category_stages');
    if (!empty($category_stages)) {
        $CI->db->where_in('tbl_category_stages.id', $category_stages);
    }
    $query = $CI->db->get()->result_array();
    $category = array();
    $dem = 0;
    foreach ($query as $key => $item) {

        $CI->db->select('*');
        $CI->db->where('category_stages', $item['id']);
        $CI->db->from('tbl_stages');
        $detail = $CI->db->get()->result_array();
        if (!empty($detail)) {
            $item['id'] = 'main__' . $item['id'];
            $item['main'] = 1;
            $item['name'] = $item['name'];
            $category[$dem] = $item;
            $dem++;
            foreach ($detail as $k => $v) {
                $v['id'] = 'detail__' . $v['id'];
                $v['main'] = 0;
                $v['name'] = '&nbsp;&nbsp;&nbsp;&nbsp;➪ ' . $v['name'];
                $category[$dem] = $v;
                $dem++;
            }
        }
    }
    return $category;
}
function GetNumberPrint($id = 0, $type = 0)
{
    $CI = &get_instance();
    $number = get_table_where('tblnumber_print', array('id_old' => $id, 'type' => $type), '', 'row');
    if (empty($number)) {
        $ins = array();
        $ins['type'] = $type;
        $ins['number'] = 1;
        $ins['date'] = date('Y-m-d H:i:s');
        $ins['staff'] = get_staff_user_id();
        $ins['id_old'] = $id;
        $CI->db->insert('tblnumber_print', $ins);
        if ($type == 1) {
            $get_code = get_table_where('tbltransfer_warehouse', array('id' => $id), '', 'row');
            activity_log_v2('transfer', 'tbltransfer_warehouse', $id, $get_code->prefix . '-' . $get_code->code, 'In phiếu lần thứ ' . $ins['number'] . ' vào lúc ' . _dt($ins['date']));
        }
        if ($type == 2) {
            $get_code = get_table_where('tbl_suggest_exporting', array('id' => $id), '', 'row');
            activity_log_v2('exporting_producion', 'tbl_suggest_exporting', $id, $get_code->reference_stock, 'In phiếu lần thứ ' . $ins['number'] . ' vào lúc ' . _dt($ins['date']));
        }
        if ($type == 3) {
            $get_code = get_table_where('tblexport_different', array('id' => $id), '', 'row');
            activity_log_v2('warehouse', 'tblexport_different', $id, $get_code->prefix . '-' . $get_code->code, 'In phiếu lần thứ ' . $ins['number'] . ' vào lúc ' . _dt($ins['date']));
        }
        return $ins;
    } else {
        $ins = array();
        $ins['type'] = $type;
        $ins['number'] = $number->number + 1;
        $ins['date'] = date('Y-m-d H:i:s');
        $ins['staff'] = get_staff_user_id();
        $ins['id_old'] = $id;
        $CI->db->update('tblnumber_print', $ins, array('id' => $number->id));
        if ($type == 1) {
            $get_code = get_table_where('tbltransfer_warehouse', array('id' => $id), '', 'row');
            activity_log_v2('transfer', 'tbltransfer_warehouse', $id, $get_code->prefix . '-' . $get_code->code, 'In phiếu lần thứ ' . $ins['number'] . ' vào lúc ' . _dt($ins['date']));
        }
        if ($type == 2) {
            $get_code = get_table_where('tbl_suggest_exporting', array('id' => $id), '', 'row');
            activity_log_v2('exporting_producion', 'tbl_suggest_exporting', $id, $get_code->reference_stock, 'In phiếu lần thứ ' . $ins['number'] . ' vào lúc ' . _dt($ins['date']));
        }
        if ($type == 3) {
            $get_code = get_table_where('tblexport_different', array('id' => $id), '', 'row');
            activity_log_v2('warehouse', 'tblexport_different', $id, $get_code->prefix . '-' . $get_code->code, 'In phiếu lần thứ ' . $ins['number'] . ' vào lúc ' . _dt($ins['date']));
        }
        return $ins;
    }
}
// function recursiveCategoryStages()
// {
//     $CI = &get_instance();

//     $CI->db->select('*');
//     $CI->db->where('is_in', 1);
//     $CI->db->from('tbl_category_stages');
//     $query = $CI->db->get()->result_array();
//     $category = array();
//     $dem = 0;
//     foreach ($query as $key => $item) {

//         $CI->db->select('*');
//         $CI->db->where('category_stages', $item['id']);
//         $CI->db->from('tbl_stages');
//         $detail = $CI->db->get()->result_array();
//         if (!empty($detail)) {
//             $item['id'] = 'main__' . $item['id'];
//             $item['name'] = $item['name'];
//             $category[$dem]['name'] = $item;
//             foreach ($detail as $k => $v) {
//                 $detail[$k]['id'] = $v['id'];
//                 $detail[$k]['name'] = '&nbsp;&nbsp;&nbsp;&nbsp;➪ ' . $v['name'];
//             }
//             $category[$dem]['detail'] = $detail;
//             $dem++;
//         }
//     }
//     return $category;
// }
function getStartInventory_v2($product_id = null, $type = null, $warehouse_id = null, $startDate = null, $localtion = null)
{
    $CI = &get_instance();
    $exists_quantity = 0;
    if (is_numeric($product_id) && !empty($startDate) && !empty($type)) {
        if ($type == 'product') {
            $_type = 'products';
            $__type = 'product';
        } else {
            if ($type == 'nvl') {
                $_type = 'materials';
                $__type = 'materials';
            } else {
                if ($type == 'tools') {
                    $_type = 'tools_supplies';
                    $__type = 'tools_supplies';
                }
            }
        }
        //Nhap
        // $CI->db->select_sum('quantity_net');
        // $CI->db->join('tblimport', 'tblimport.id=tblimport_items.id_import', 'left');
        // if ($warehouse_id) {
        //  $CI->db->where('tblimport.warehouse_id', $warehouse_id);
        // }
        // if ($startDate) {
        //  $CI->db->where('tblimport.date <', $startDate);
        // }
        // if ($localtion) {
        //  $CI->db->where('tblimport_items.localtion_warehouses_id', $localtion);
        // }
        // $quantity_import = $CI->db->get_where(
        //  'tblimport_items',
        //  array('product_id' => $product_id, 'type' => $type)
        // )->row()->quantity_net;

        $CI->db->select('Sum(quantity_payment) as quantity_net,SUM(quantity_payment*price)  as price');
        $CI->db->join('tblimport', 'tblimport.id=tblimport_items.id_import', 'left');
        if ($warehouse_id) {
            $CI->db->where('tblimport.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblimport.date <', $startDate);
        }
        $CI->db->where('warehouseman_id != 0');

        $import = $CI->db->get_where(
            'tblimport_items',
            array('product_id' => $product_id, 'type' => $type)
        )->row();
        $quantity_import = $import->price;
        if ($quantity_import == 0) {
            $quantity_import = getpriceimport($product_id, $type) * $import->quantity_net;
        }
        // -------------------------------
        // xuất kho
        $CI->db->select('tbl_delivery_items.*');
        $CI->db->join('tbl_deliveries', 'tbl_deliveries.id=tbl_delivery_items.delivery_id', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbl_delivery_items.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_deliveries.date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbl_delivery_items.location_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_export = $CI->db->get_where(
            'tbl_delivery_items',
            array('item_id' => $product_id, 'type_item' => $_type)
        )->result_array();
        foreach ($quantity_export as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity += $quantitys * $price;
                }
            }
        }
        // ------------------------------
        // trả hàng ncc
        $CI->db->select('tblreturn_suppliers_items.*');
        $CI->db->join('tblreturn_suppliers', 'tblreturn_suppliers.id=tblreturn_suppliers_items.id_return', 'left');
        if ($warehouse_id) {
            $CI->db->where('tblreturn_suppliers.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblreturn_suppliers.date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tblreturn_suppliers_items.localtion_warehouses_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_return = $CI->db->get_where(
            'tblreturn_suppliers_items',
            array('product_id' => $product_id, 'type' => $type)
        )->result_array();
        foreach ($quantity_return as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity += $quantitys * $price;
                }
            }
        }
        //xuat khac
        $CI->db->select('tbltblexport_different_items.*,tblexport_different.id as idd,tblexport_different.warehouseman_date');
        $CI->db->join('tblexport_different', 'tblexport_different.id=tbltblexport_different_items.id_export_different', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbltblexport_different_items.warehouses_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblexport_different.date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbltblexport_different_items.localtion_warehouses_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_adju_G = $CI->db->get_where(
            'tbltblexport_different_items',
            array('product_id' => $product_id, 'tbltblexport_different_items.type' => $type)
        )->result_array();

        foreach ($quantity_adju_G as $key => $value) {
            if (!empty($value['id_import'])) {
                $itemss = $value['id_import'];
                $array = explode('|', $itemss);
                foreach ($array as $ka => $va) {
                    if (!empty($va)) {
                        $waretos = explode('-', $va);
                        $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                        $price = $quantity_nets->price;
                        if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                            $quantitys = $waretos[4];
                        } else {
                            $quantitys = $waretos[3];
                        }
                        if (empty($quantitys) && ((($quantitys) != 0) || (($quantitys) == ''))) {
                            $quantitys = $value['quantity_payment'];
                        }
                        if ($price == 0) {
                            $price = getpriceimport($product_id, $type);
                        }
                        $exists_quantity += $quantitys * $price;
                    }
                }
            } else {
                $quantity_nets = get_table_where('tblwarehouse_product', array('product_id' => $value['product_id'], 'type_items' => $value['type'], 'localtion' => $value['localtion_warehouses_id'], 'date_warehouse <=' => $value['warehouseman_date']), 'date_warehouse DESC', 'row');
                if (!empty($quantity_nets)) {
                    $exists_quantity += $value['quantity_payment'] * $quantity_nets->price;
                } else {
                    $exists_quantity += 0;
                }
            }
        }

        // ------------------------------
        // điều chỉnh tăng
        $CI->db->select('Sum(quantity_payment) as quantity_net,SUM(quantity_payment*price)  as price');
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbladjusted.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbladjusted.date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbladjusted_items.localtion', $localtion);
        }
        $CI->db->where('tbladjusted.type', 1);
        $_adju_T = $CI->db->get_where(
            'tbladjusted_items',
            array('product_id' => $product_id, 'tbladjusted_items.type' => $type)
        )->row();
        $quantity_adju_T = $_adju_T->price;
        if ($quantity_adju_T == 0) {
            $quantity_adju_T = getpriceimport($product_id, $type) * $_adju_T->quantity_net;
        }
        // ------------------------------
        // điều chỉnh giảm
        $CI->db->select('tbladjusted_items.*');
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbladjusted.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbladjusted.date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbladjusted_items.localtion', $localtion);
        }
        $CI->db->where('tbladjusted.type', 2);
        $quantity_adju_G = $CI->db->get_where(
            'tbladjusted_items',
            array('product_id' => $product_id, 'tbladjusted_items.type' => $type)
        )->result_array();

        foreach ($quantity_adju_G as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity += $quantitys * $price;
                }
            }
        }

        // ------------------------------
        // chuyển kho nhận
        // $CI->db->select('Sum(quantity_net) as quantity_net,SUM(quantity_net*price)  as price');
        // $CI->db->join(
        //  'tbltransfer_warehouse',
        //  'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer',
        //  'left'
        // );
        // if ($warehouse_id) {
        //  $CI->db->where('tbltransfer_warehouse_detail.warehouses_to', $warehouse_id);
        // }
        // if ($startDate) {
        //  $CI->db->where('tbltransfer_warehouse.date <', $startDate);
        // }
        // if ($localtion) {
        //  $CI->db->where('tbltransfer_warehouse_detail.localtion_to', $localtion);
        // }
        // $CI->db->where('warehouseman_id != 0');
        // $quantity_tranfer_N = $CI->db->get_where(
        //  'tbltransfer_warehouse_detail',
        //  array('id_items' => $product_id, 'type' => $type)
        // )->row()->price;

        // tien chuyen gia nhan
        $quantity_tranfer_N = 0;
        $CI->db->select('tbltransfer_warehouse_detail.*,tbltransfer_warehouse.id as idd');
        $CI->db->join(
            'tbltransfer_warehouse',
            'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where('tbltransfer_warehouse_detail.warehouses_to', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbltransfer_warehouse.date <', $startDate);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_tranfer_D = $CI->db->get_where(
            'tbltransfer_warehouse_detail',
            array('id_items' => $product_id, 'type' => $type)
        )->result_array();

        foreach ($quantity_tranfer_D as $key => $value) {
            $quantity_nets = get_table_where('tblwarehouse_product', array('import_id' => $value['idd'], 'type_export' => 2, 'product_id' => $value['id_items'], 'product_id' => $value['id_items'], 'type_items' => $value['type'], 'localtion' => $value['localtion_to']));

            foreach ($quantity_nets as $ka => $vs) {
                if ($vs['price'] == 0) {
                    $vs['price'] = getpriceimport($product_id, $type);
                }
                $quantity_tranfer_N += $vs['product_quantity_payment'] * $vs['price'];
            }
        }
        // foreach ($quantity_tranfer_D as $key => $value) {
        //  $itemss = $value['id_import'];
        //  $array = explode('|', $itemss);
        //  foreach ($array as $ka => $va) {
        //      if (!empty($va)) {
        //          $waretos = explode('-', $va);
        //          $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
        //          $price = $quantity_nets->price;
        //          $quantitys = $waretos[1];
        //          $quantity_tranfer_N += $quantitys * $price;
        //      }
        //  }
        // }
        // ------------------------------
        // chuyển kho đi
        $CI->db->select('tbltransfer_warehouse_detail.*,tbltransfer_warehouse.id as idd,tbltransfer_warehouse.warehouseman_date');
        $CI->db->join(
            'tbltransfer_warehouse',
            'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where('tbltransfer_warehouse_detail.warehouses_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbltransfer_warehouse.date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbltransfer_warehouse_detail.localtion_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_tranfer_D = $CI->db->get_where(
            'tbltransfer_warehouse_detail',
            array('id_items' => $product_id, 'type' => $type)
        )->result_array();

        foreach ($quantity_tranfer_D as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');

                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity += $quantitys * $price;
                }
            }
        }

        // foreach ($quantity_tranfer_D as $key => $value) {

        //  $quantity_nets = get_table_where('tblwarehouse_product', array('product_id' => $value['id_items'], 'product_id' => $value['id_items'], 'type_items' => $value['type'], 'localtion' => $value['localtion_id'], 'date_warehouse <=' => $value['warehouseman_date']), 'date_warehouse DESC', 'row');
        //  if (!empty($quantity_nets)) {
        //      $exists_quantity += $value['quantity_net']*$quantity_nets->price;
        //  } else {
        //      $exists_quantity += 0;
        //  }


        //  // $quantity_nets = get_table_where('tblwarehouse_product', array('import_id' => $value['idd'], 'type_export' => 2, 'product_id' => $value['id_items'], 'product_id' => $value['id_items'], 'type_items' => $value['type'], 'localtion' => $value['localtion_id']), '', 'row');
        //  // if (!empty($quantity_nets)) {
        //  //  $exists_quantity += $value['quantity_net']*$quantity_nets->price;
        //  // } else {
        //  //  $exists_quantity += 0;
        //  // }
        //  // $itemss = $value['id_import'];
        //  // $array = explode('|', $itemss);
        //  // foreach ($array as $ka => $va) {
        //  //  if (!empty($va)) {
        //  //      $waretos = explode('-', $va);
        //  //      $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
        //  //      $price = $quantity_nets->price;
        //  //      $quantitys = $waretos[1];
        //  //      $exists_quantity += $quantitys * $price;
        //  //  }
        //  // }
        // }
        // ------------------------------
        // xuất kho sản xuất
        $CI->db->select('tbl_suggest_exporting_items.*');
        $CI->db->join(
            'tbl_suggest_exporting',
            'tbl_suggest_exporting.id=tbl_suggest_exporting_items.suggest_exporting_id',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where('tbl_suggest_exporting_items.warehouse_item_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_suggest_exporting.date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbl_suggest_exporting_items.location_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');
        $CI->db->like('type_item', $_type);
        $quantity_purchase_products = $CI->db->get_where(
            'tbl_suggest_exporting_items',
            array('item_id' => $product_id)
        )->result_array();
        foreach ($quantity_purchase_products as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity += $quantitys * $price;
                }
            }
        }
        // ------------------------------
        // nhập kho thành phẩm
        // $CI->db->select_sum('quantity');
        $CI->db->select('Sum(quantity) as quantity,SUM(quantity*price)  as price');
        $CI->db->join(
            'tbl_purchase_products',
            'tbl_purchase_products.id=tbl_purchase_product_items.purchase_product_id',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where('tbl_purchase_products.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_purchase_products.date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbl_purchase_product_items.location_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $CI->db->like('tbl_purchase_product_items.type_item', $__type);

        $purchase_products = $CI->db->get_where(
            'tbl_purchase_product_items',
            array('item_id' => $product_id)
        )->row();

        $quantity_purchase_products = $purchase_products->price;
        if ($quantity_purchase_products == 0) {
            $quantity_purchase_products = getpriceimport($product_id, $type) * 0;
        }

        $CI->db->select('Sum(quantity) as quantity,SUM(quantity*price)  as price');
        $CI->db->join(
            'tbl_purchase_internal',
            'tbl_purchase_internal.id=tbl_purchase_internal_items.purchase_internal_id',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where('tbl_purchase_internal.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbl_purchase_internal.date >=', $startDate . ' 00:00:00');
            $CI->db->where('tbl_purchase_internal.date <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');

        $CI->db->like('tbl_purchase_internal_items.type_item', $_type);

        $purchase_internal = $CI->db->get_where(
            'tbl_purchase_internal_items',
            array('item_id' => $product_id)
        )->row();
        $quantity_purchase_internal = $purchase_internal->price;
        if ($quantity_purchase_internal == 0) {
            $quantity_purchase_internal = getpriceimport($product_id, $type) * 0;
        }
        // ------------------------------
        $exists_quantity = $quantity_import + $quantity_adju_T + $quantity_tranfer_N + $quantity_purchase_products - $exists_quantity + $quantity_purchase_internal;
    }
    return $exists_quantity;
}
function getStartInventory_trongki($product_id = null, $type = null, $warehouse_id = null, $startDate = null, $startEnd = null)
{
    $CI = &get_instance();
    $exists_quantity_import = 0;
    $exists_quantity_export = 0;
    $data['exists_quantity_import'] = $exists_quantity_import;
    $data['exists_quantity_export'] = $exists_quantity_export;
    if ($startDate) {
        $startDate = $startDate . ' 00:00:00';
    }
    if ($startEnd) {
        $startEnd = $startEnd . ' 23:59:59';
    }
    if (is_numeric($product_id) && !empty($type)) {
        if ($type == 'product') {
            $_type = 'products';
            $__type = 'product';
        } else {
            if ($type == 'nvl') {
                $_type = 'materials';
                $__type = 'materials';
            } else {
                if ($type == 'tools') {
                    $_type = 'tools_supplies';
                    $__type = 'tools_supplies';
                }
            }
        }
        //Nhap
        $CI->db->select('Sum(quantity_payment) as quantity_net,SUM(quantity_payment*price)  as price');
        $CI->db->join('tblimport', 'tblimport.id=tblimport_items.id_import', 'left');
        if ($warehouse_id) {
            $CI->db->where('tblimport.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblimport.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tblimport.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');

        $import = $CI->db->get_where(
            'tblimport_items',
            array('product_id' => $product_id, 'type' => $type)
        )->row();
        $quantity_import = $import->price;
        if ($quantity_import == 0) {
            $quantity_import = getpriceimport($product_id, $type) * $import->quantity_net;
        }

        $exists_quantity_import += $quantity_import;
        // -------------------------------
        // xuất kho
        $CI->db->select('tbl_delivery_items.*');
        $CI->db->join('tbl_deliveries', 'tbl_deliveries.id=tbl_delivery_items.delivery_id', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbl_delivery_items.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_deliveries.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbl_deliveries.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_export = $CI->db->get_where(
            'tbl_delivery_items',
            array('item_id' => $product_id, 'type_item' => $_type)
        )->result_array();
        foreach ($quantity_export as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity_export += $quantitys * $price;
                }
            }
        }

        // ------------------------------
        // trả hàng ncc
        $CI->db->select('tblreturn_suppliers_items.*');
        $CI->db->join('tblreturn_suppliers', 'tblreturn_suppliers.id=tblreturn_suppliers_items.id_return', 'left');
        if ($warehouse_id) {
            $CI->db->where('tblreturn_suppliers.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblreturn_suppliers.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tblreturn_suppliers.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_return = $CI->db->get_where(
            'tblreturn_suppliers_items',
            array('product_id' => $product_id, 'type' => $type)
        )->result_array();
        foreach ($quantity_return as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity_export += $quantitys * $price;
                }
            }
        }
        //xuat khac
        $CI->db->select('tbltblexport_different_items.*,tblexport_different.id as idd,tblexport_different.warehouseman_date');
        $CI->db->join('tblexport_different', 'tblexport_different.id=tbltblexport_different_items.id_export_different', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbltblexport_different_items.warehouses_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblexport_different.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tblexport_different.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_adju_G = $CI->db->get_where(
            'tbltblexport_different_items',
            array('product_id' => $product_id, 'tbltblexport_different_items.type' => $type)
        )->result_array();
        foreach ($quantity_adju_G as $key => $value) {
            if (!empty($value['id_import'])) {
                $itemss = $value['id_import'];
                $array = explode('|', $itemss);
                foreach ($array as $ka => $va) {
                    if (!empty($va)) {
                        $waretos = explode('-', $va);
                        $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                        $price = $quantity_nets->price;
                        if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                            $quantitys = $waretos[4];
                        } else {
                            $quantitys = $waretos[3];
                        }

                        if (empty($quantitys) && ((($quantitys) != 0) || (($quantitys) == ''))) {
                            $quantitys = $value['quantity_payment'];
                        }

                        if ($price == 0) {
                            $price = getpriceimport($product_id, $type);
                        }

                        $exists_quantity_export += $quantitys * $price;
                    }
                }
            } else {
                $quantity_nets = get_table_where('tblwarehouse_product', array('product_id' => $value['product_id'], 'type_items' => $value['type'], 'localtion' => $value['localtion_warehouses_id'], 'date_warehouse <=' => $value['warehouseman_date']), 'date_warehouse DESC', 'row');
                if (!empty($quantity_nets)) {
                    $exists_quantity_export += $value['quantity_payment'] * $quantity_nets->price;
                } else {
                    $exists_quantity_export += 0;
                }
            }
        }

        // ------------------------------
        // điều chỉnh tăng
        $CI->db->select('Sum(quantity_payment) as quantity_net,SUM(quantity_net*price)  as price');
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbladjusted.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbladjusted.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbladjusted.date <=', $startEnd);
        }
        $CI->db->where('tbladjusted.type', 1);
        $_adju_T = $CI->db->get_where(
            'tbladjusted_items',
            array('product_id' => $product_id, 'tbladjusted_items.type' => $type)
        )->row();
        $quantity_adju_T = $_adju_T->price;
        if ($quantity_adju_T == 0) {
            $quantity_adju_T = getpriceimport($product_id, $type) * $_adju_T->quantity_net;
        }
        $exists_quantity_import += $quantity_adju_T;

        // ------------------------------
        // điều chỉnh giảm
        $CI->db->select('tbladjusted_items.*');
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where('tbladjusted.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbladjusted.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbladjusted.date <=', $startEnd);
        }
        $CI->db->where('tbladjusted.type', 2);
        $quantity_adju_G = $CI->db->get_where(
            'tbladjusted_items',
            array('product_id' => $product_id, 'tbladjusted_items.type' => $type)
        )->result_array();

        foreach ($quantity_adju_G as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity_export += $quantitys * $price;
                }
            }
        }

        // ------------------------------
        // chuyển kho nhận
        // tien chuyen gia nhan
        $quantity_tranfer_N = 0;
        $CI->db->select('tbltransfer_warehouse_detail.*,tbltransfer_warehouse.id as idd');
        $CI->db->join(
            'tbltransfer_warehouse',
            'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where('tbltransfer_warehouse_detail.warehouses_to', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbltransfer_warehouse.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbltransfer_warehouse.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_tranfer_D = $CI->db->get_where(
            'tbltransfer_warehouse_detail',
            array('id_items' => $product_id, 'type' => $type)
        )->result_array();

        foreach ($quantity_tranfer_D as $key => $value) {
            $quantity_nets = get_table_where('tblwarehouse_product', array('import_id' => $value['idd'], 'type_export' => 2, 'product_id' => $value['id_items'], 'product_id' => $value['id_items'], 'type_items' => $value['type'], 'localtion' => $value['localtion_to']));
            foreach ($quantity_nets as $ka => $vs) {
                if ($vs['price'] == 0) {
                    $vs['price'] = getpriceimport($product_id, $type);
                }
                $exists_quantity_import += $vs['product_quantity_payment'] * $vs['price'];
            }
        }
        // }
        // ------------------------------
        // chuyển kho đi
        $CI->db->select('tbltransfer_warehouse_detail.*,tbltransfer_warehouse.id as idd,tbltransfer_warehouse.warehouseman_date');
        $CI->db->join(
            'tbltransfer_warehouse',
            'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where('tbltransfer_warehouse_detail.warehouses_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbltransfer_warehouse.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbltransfer_warehouse.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_tranfer_D = $CI->db->get_where(
            'tbltransfer_warehouse_detail',
            array('id_items' => $product_id, 'type' => $type)
        )->result_array();

        foreach ($quantity_tranfer_D as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);

                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    // $quantitys = $waretos[1];
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($quantitys > $value['quantity_payment']) {
                        $quantitys = $value['quantity_payment'];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity_export += $quantitys * $price;
                }
            }
        }

        // xuất kho sản xuất
        $CI->db->select('tbl_suggest_exporting_items.*');
        $CI->db->join(
            'tbl_suggest_exporting',
            'tbl_suggest_exporting.id=tbl_suggest_exporting_items.suggest_exporting_id',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where('tbl_suggest_exporting_items.warehouse_item_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_suggest_exporting.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbl_suggest_exporting.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');
        $CI->db->like('type_item', $_type);
        $quantity_purchase_products = $CI->db->get_where(
            'tbl_suggest_exporting_items',
            array('item_id' => $product_id)
        )->result_array();
        foreach ($quantity_purchase_products as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity_export += $quantitys * $price;
                }
            }
        }
        // ------------------------------
        // nhập kho thành phẩm
        // $CI->db->select_sum('quantity');
        $CI->db->select('Sum(quantity) as quantity,SUM(quantity*price)  as price');
        $CI->db->join(
            'tbl_purchase_products',
            'tbl_purchase_products.id=tbl_purchase_product_items.purchase_product_id',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where('tbl_purchase_products.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_purchase_products.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbl_purchase_products.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');

        $CI->db->like('tbl_purchase_product_items.type_item', $__type);

        $purchase_products = $CI->db->get_where(
            'tbl_purchase_product_items',
            array('item_id' => $product_id)
        )->row();

        $quantity_purchase_products = $purchase_products->price;
        if ($quantity_purchase_products == 0) {
            $quantity_purchase_products = getpriceimport($product_id, $type) * 0;
        }
        $exists_quantity_import += $quantity_purchase_products;

        $CI->db->select('Sum(quantity) as quantity,SUM(quantity*price)  as price');
        $CI->db->join(
            'tbl_purchase_internal',
            'tbl_purchase_internal.id=tbl_purchase_internal_items.purchase_internal_id',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where('tbl_purchase_internal.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbl_purchase_internal.date >=', $startDate . ' 00:00:00');
            $CI->db->where('tbl_purchase_internal.date <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');

        $CI->db->like('tbl_purchase_internal_items.type_item', $_type);

        $purchase_internal = $CI->db->get_where(
            'tbl_purchase_internal_items',
            array('item_id' => $product_id)
        )->row();
        $quantity_purchase_internal = $purchase_internal->price;
        if ($quantity_purchase_internal == 0) {
            $quantity_purchase_internal = getpriceimport($product_id, $type) * 0;
        }
        $exists_quantity_import += $quantity_purchase_internal;

        // ------------------------------
        $data['exists_quantity_import'] = $exists_quantity_import;
        $data['exists_quantity_export'] = $exists_quantity_export;

        // $exists_quantity = $quantity_import  + $quantity_adju_T + $quantity_tranfer_N + $quantity_purchase_products - $exists_quantity + $quantity_purchase_internal;
    }
    return $data;
}
function getpriceimportwarehouse($items_id = '', $type_items = '', $type_export = '')
{
    if ($type_items == 'products') {
        $type_items = 'product';
    } else
        if ($type_items == 'materials') {
        $type_items = 'nvl';
    } else
            if ($type_items == 'tools_supplies') {
        $type_items = 'tools';
    } else
                if ($type_items == 'semi_products') {
        $type_items = 'product';
    }
    $CI = &get_instance();
    $CI->db->select('tblwarehouse_product.price');
    $CI->db->where('tblwarehouse_product.product_id', $items_id);
    $CI->db->where('tblwarehouse_product.type_items', $type_items);
    $CI->db->where('tblwarehouse_product.type_export', $type_export);
    $ktr_supp = $CI->db->get('tblwarehouse_product')->row();
    $price_ch = 0;
    if (!empty($ktr_supp)) {
        $price_ch = $ktr_supp->price;
    }
    return $price_ch;
}
function getpriceimport($items_id = '', $type_items = '')
{
    if ($type_items == 'products') {
        $type_items = 'product';
    } else
        if ($type_items == 'materials') {
        $type_items = 'nvl';
    } else
            if ($type_items == 'tools_supplies') {
        $type_items = 'tools';
    } else
                if ($type_items == 'semi_products') {
        $type_items = 'product';
    }
    $CI = &get_instance();
    $CI->db->select('tblimport_items.price');
    $CI->db->where('tblimport_items.product_id', $items_id);
    $CI->db->where('tblimport_items.type', $type_items);
    $CI->db->limit(1);
    $CI->db->order_by('tblimport_items.id DESC');
    $ktr_supp = $CI->db->get('tblimport_items')->row();
    $price_ch = 0;
    if (!empty($ktr_supp)) {
        $price_ch = $ktr_supp->price;
    }
    return $price_ch;
}
function getStartInventoryArray($product_id = NULL, $type = NULL, $warehouse_id = NULL, $startDate = NULL, $localtion = NULL)
{
    $CI = &get_instance();
    $exists_quantity = 0;
    if (is_numeric($product_id) && !empty($startDate) && !empty($type)) {
        if ($type == 'product') {
            $_type = 'products';
        } else
            if ($type == 'nvl') {
            $_type = 'materials';
        } else
                if ($type == 'tools') {
            $_type = 'tools_supplies';
        }
        //Nhap
        $CI->db->select_sum('quantity_stock');
        $CI->db->join('tblimport', 'tblimport.id=tblimport_items.id_import', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tblimport.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblimport.warehouseman_date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tblimport_items.localtion_warehouses_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_import = $CI->db->get_where('tblimport_items', array('product_id' => $product_id, 'type' => $type))->row()->quantity_stock;
        // -------------------------------
        // xuất kho
        $CI->db->select_sum('quantity_stock');
        $CI->db->join('tbl_deliveries', 'tbl_deliveries.id=tbl_delivery_items.delivery_id', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbl_delivery_items.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_deliveries.date_warehouseman <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbl_delivery_items.location_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_export = $CI->db->get_where('tbl_delivery_items', array('item_id' => $product_id, 'type_item' => $_type))->row()->quantity_stock;
        // ------------------------------
        // trả hàng ncc
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tblreturn_suppliers', 'tblreturn_suppliers.id=tblreturn_suppliers_items.id_return', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tblreturn_suppliers.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblreturn_suppliers.data_warehouseman <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tblreturn_suppliers_items.localtion_warehouses_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_return = $CI->db->get_where('tblreturn_suppliers_items', array('product_id' => $product_id, 'type' => $type))->row()->quantity_net;
        // ------------------------------
        // điều chỉnh tăng
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbladjusted.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbladjusted.date_create <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbladjusted_items.localtion', $localtion);
        }
        $CI->db->where('tbladjusted.type', 1);
        $quantity_adju_T = $CI->db->get_where('tbladjusted_items', array('product_id' => $product_id, 'tbladjusted_items.type' => $type))->row()->quantity_net;
        // ------------------------------
        // điều chỉnh giảm
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbladjusted.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbladjusted.date_create <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbladjusted_items.localtion', $localtion);
        }
        $CI->db->where('tbladjusted.type', 2);
        $quantity_adju_G = $CI->db->get_where('tbladjusted_items', array('product_id' => $product_id, 'tbladjusted_items.type' => $type))->row()->quantity_net;
        // ------------------------------
        // chuyển kho nhận
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbltransfer_warehouse', 'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbltransfer_warehouse_detail.warehouses_to', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbltransfer_warehouse.warehouseman_date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbltransfer_warehouse_detail.localtion_to', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_tranfer_N = $CI->db->get_where('tbltransfer_warehouse_detail', array('id_items' => $product_id, 'type' => $type))->row()->quantity_net;
        // ------------------------------
        // chuyển kho đi
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbltransfer_warehouse', 'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbltransfer_warehouse_detail.warehouses_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbltransfer_warehouse.warehouseman_date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbltransfer_warehouse_detail.localtion_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_tranfer_D = $CI->db->get_where('tbltransfer_warehouse_detail', array('id_items' => $product_id, 'type' => $type))->row()->quantity_net;
        // ------------------------------
        // xuất kho sản xuất
        $CI->db->select_sum('quantity_warehouse');
        $CI->db->join('tbl_suggest_exporting', 'tbl_suggest_exporting.id=tbl_suggest_exporting_items.suggest_exporting_id', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbl_suggest_exporting_items.warehouse_item_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_suggest_exporting.date_warehouseman <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbl_suggest_exporting_items.location_id', $localtion);
        }
        $CI->db->like('type_item', $_type);
        $CI->db->where('warehouseman_id != 0');

        $suggest_exporting_items = $CI->db->get_where('tbl_suggest_exporting_items', array('item_id' => $product_id))->row()->quantity_warehouse;
        // ------------------------------
        // nhập kho thành phẩm
        $CI->db->select_sum('quantity');
        $CI->db->join('tbl_purchase_products', 'tbl_purchase_products.id=tbl_purchase_product_items.purchase_product_id', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbl_purchase_products.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_purchase_products.date_warehouseman <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbl_purchase_product_items.location_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_purchase_products = $CI->db->get_where('tbl_purchase_product_items', array('item_id' => $product_id, 'type_item' => $_type))->row()->quantity;

        // nhập kho phe lieu
        $CI->db->select_sum('quantity');
        $CI->db->join('tbl_purchase_internal', 'tbl_purchase_internal.id=tbl_purchase_internal_items.purchase_internal_id', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbl_purchase_internal.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_purchase_internal.date_warehouseman <', $startDate);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_purchase_internal = $CI->db->get_where('tbl_purchase_internal_items', array('item_id' => $product_id, 'type_item' => $_type))->row()->quantity;

        // ------------------------------
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tblexport_different', 'tblexport_different.id=tbltblexport_different_items.id_export_different', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbltblexport_different_items.warehouses_id', $warehouse_id);
        }
        if (!empty($startDate)) {
            $CI->db->where('tblexport_different.warehouseman_date <', $startDate . ' 00:00:00');
        }

        $CI->db->where('warehouseman_id != 0');
        $quantity_export_different = $CI->db->get_where('tbltblexport_different_items', array('product_id' => $product_id, 'type' => $type))->row()->quantity_net;


        // nhập kho thành phẩm
        $CI->db->select_sum('quantity');
        $CI->db->join('tbl_returned_goods', 'tbl_returned_goods.id=tbl_returned_goods_items.returned_goods_id', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbl_returned_goods_items.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate)) {
            $CI->db->where('tbl_returned_goods.warehouseman_date <', $startDate . ' 00:00:00');
        }
        $CI->db->where('warehouseman_id != 0');

        $returned_goods = $CI->db->get_where('tbl_returned_goods_items', array('item_id' => $product_id, 'type_item' => $_type))->row()->quantity;

        $exists_quantity = $quantity_import - $quantity_export - $quantity_return + $quantity_adju_T - $quantity_adju_G + $quantity_tranfer_N - $quantity_tranfer_D + $quantity_purchase_products - $suggest_exporting_items - $quantity_export_different + $returned_goods + $quantity_purchase_internal;
    }
    return $exists_quantity;
}
function getStartInventory_exportArray($product_id = NULL, $type = NULL, $warehouse_id = NULL, $startDate = NULL, $startEnd = NULL)
{
    $CI = &get_instance();
    $exists_quantity = 0;
    if (is_numeric($product_id) && !empty($type)) {
        if ($type == 'product') {
            $_type = 'products';
        } else
            if ($type == 'nvl') {
            $_type = 'materials';
        } else
                if ($type == 'tools') {
            $_type = 'tools_supplies';
        }
        // xuất kho
        $CI->db->select_sum('quantity_stock');
        $CI->db->join('tbl_deliveries', 'tbl_deliveries.id=tbl_delivery_items.delivery_id', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbl_delivery_items.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbl_deliveries.date_warehouseman >=', $startDate . ' 00:00:00');
            $CI->db->where('tbl_deliveries.date_warehouseman <=', $startEnd . ' 23:59:59');
        }

        $CI->db->where('warehouseman_id != 0');
        $quantity_export = $CI->db->get_where('tbl_delivery_items', array('item_id' => $product_id, 'type_item' => $_type))->row()->quantity_stock;
        // ------------------------------
        // trả hàng ncc
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tblreturn_suppliers', 'tblreturn_suppliers.id=tblreturn_suppliers_items.id_return', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tblreturn_suppliers.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tblreturn_suppliers.data_warehouseman >=', $startDate . ' 00:00:00');
            $CI->db->where('tblreturn_suppliers.data_warehouseman <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');


        $quantity_return = $CI->db->get_where('tblreturn_suppliers_items', array('product_id' => $product_id, 'type' => $type))->row()->quantity_net;
        // ------------------------------
        // điều chỉnh giảm
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbladjusted.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbladjusted.date_create >=', $startDate . ' 00:00:00');
            $CI->db->where('tbladjusted.date_create <=', $startEnd . ' 23:59:59');
        }

        $CI->db->where('tbladjusted.type', 2);
        $quantity_adju_G = $CI->db->get_where('tbladjusted_items', array('product_id' => $product_id, 'tbladjusted_items.type' => $type))->row()->quantity_net;
        // ------------------------------
        // chuyển kho đi
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbltransfer_warehouse', 'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbltransfer_warehouse_detail.warehouses_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbltransfer_warehouse.warehouseman_date >=', $startDate . ' 00:00:00');
            $CI->db->where('tbltransfer_warehouse.warehouseman_date <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_tranfer_D = $CI->db->get_where('tbltransfer_warehouse_detail', array('id_items' => $product_id, 'type' => $type))->row()->quantity_net;
        // var_dump($CI->db->last_query());die;
        // ------------------------------
        // xuất kho sản xuất
        $CI->db->select_sum('quantity_warehouse');
        $CI->db->join('tbl_suggest_exporting', 'tbl_suggest_exporting.id=tbl_suggest_exporting_items.suggest_exporting_id', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbl_suggest_exporting_items.warehouse_item_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbl_suggest_exporting.date_warehouseman >=', $startDate . ' 00:00:00');
            $CI->db->where('tbl_suggest_exporting.date_warehouseman <=', $startEnd . ' 23:59:59');
        }
        $CI->db->like('type_item', $_type);
        $CI->db->where('warehouseman_id != 0');

        $quantity_purchase_products = $CI->db->get_where('tbl_suggest_exporting_items', array('item_id' => $product_id))->row()->quantity_warehouse;
        // ------------------------------
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tblexport_different', 'tblexport_different.id=tbltblexport_different_items.id_export_different', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbltblexport_different_items.warehouses_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tblexport_different.warehouseman_date >=', $startDate . ' 00:00:00');
            $CI->db->where('tblexport_different.warehouseman_date <=', $startEnd . ' 23:59:59');
        }

        $CI->db->where('warehouseman_id != 0');
        $quantity_export_different = $CI->db->get_where('tbltblexport_different_items', array('product_id' => $product_id, 'type' => $type))->row()->quantity_net;

        $exists_quantity = $quantity_export + $quantity_return + $quantity_adju_G + $quantity_tranfer_D + $quantity_purchase_products + $quantity_export_different;
    }
    return $exists_quantity;
}
function getStartInventory_importArray($product_id = NULL, $type = NULL, $warehouse_id = NULL, $startDate = NULL, $startEnd = NULL)
{
    $CI = &get_instance();
    $exists_quantity = 0;
    if (is_numeric($product_id) && !empty($type)) {
        if ($type == 'product') {
            $_type = 'products';
        } else
            if ($type == 'nvl') {
            $_type = 'materials';
        } else
                if ($type == 'tools') {
            $_type = 'tools_supplies';
        }
        //Nhap
        $CI->db->select_sum('quantity_stock');
        $CI->db->join('tblimport', 'tblimport.id=tblimport_items.id_import', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tblimport.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tblimport.warehouseman_date >=', $startDate . ' 00:00:00');
            $CI->db->where('tblimport.warehouseman_date <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_import = $CI->db->get_where('tblimport_items', array('product_id' => $product_id, 'type' => $type))->row()->quantity_stock;
        // echo '<pre>';print_arrays($CI->db->last_query());die;
        // -------------------------------
        // điều chỉnh tăng
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbladjusted.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbladjusted.date_create >=', $startDate . ' 00:00:00');
            $CI->db->where('tbladjusted.date_create <=', $startEnd . ' 23:59:59');
        }

        $CI->db->where('tbladjusted.type', 1);
        $quantity_adju_T = $CI->db->get_where('tbladjusted_items', array('product_id' => $product_id, 'tbladjusted_items.type' => $type))->row()->quantity_net;
        // ------------------------------
        // chuyển kho nhận
        $CI->db->select_sum('quantity_net');
        $CI->db->join('tbltransfer_warehouse', 'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbltransfer_warehouse_detail.warehouses_to', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbltransfer_warehouse.warehouseman_date >=', $startDate . ' 00:00:00');
            $CI->db->where('tbltransfer_warehouse.warehouseman_date <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_tranfer_N = $CI->db->get_where('tbltransfer_warehouse_detail', array('id_items' => $product_id, 'type' => $type))->row()->quantity_net;
        // ------------------------------
        // nhập kho thành phẩm
        $CI->db->select_sum('quantity');
        $CI->db->join('tbl_purchase_products', 'tbl_purchase_products.id=tbl_purchase_product_items.purchase_product_id', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbl_purchase_products.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbl_purchase_products.date_warehouseman >=', $startDate . ' 00:00:00');
            $CI->db->where('tbl_purchase_products.date_warehouseman <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_purchase_products = $CI->db->get_where('tbl_purchase_product_items', array('item_id' => $product_id, 'type_item' => $_type))->row()->quantity;

        // nhập kho phe lieu
        $CI->db->select_sum('quantity');
        $CI->db->join('tbl_purchase_internal', 'tbl_purchase_internal.id=tbl_purchase_internal_items.purchase_internal_id', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbl_purchase_internal.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbl_purchase_internal.date_warehouseman >=', $startDate . ' 00:00:00');
            $CI->db->where('tbl_purchase_internal.date_warehouseman <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_purchase_internal = $CI->db->get_where('tbl_purchase_internal_items', array('item_id' => $product_id, 'type_item' => $_type))->row()->quantity;

        // nhập kho thành phẩm
        $CI->db->select_sum('quantity');
        $CI->db->join('tbl_returned_goods', 'tbl_returned_goods.id=tbl_returned_goods_items.returned_goods_id', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbl_returned_goods_items.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbl_returned_goods.warehouseman_date >=', $startDate . ' 00:00:00');
            $CI->db->where('tbl_returned_goods.warehouseman_date <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');

        $returned_goods = $CI->db->get_where('tbl_returned_goods_items', array('item_id' => $product_id, 'type_item' => $_type))->row()->quantity;
        // ------------------------------
        $exists_quantity = $quantity_import + $quantity_adju_T + $quantity_tranfer_N + $quantity_purchase_products + $returned_goods + $quantity_purchase_internal;
    }
    return $exists_quantity;
}
function getStartInventory_v2Array($product_id = null, $type = null, $warehouse_id = null, $startDate = null, $localtion = null)
{
    $CI = &get_instance();
    $exists_quantity = 0;
    if (is_numeric($product_id) && !empty($startDate) && !empty($type)) {
        if ($type == 'product') {
            $_type = 'products';
            $__type = 'product';
        } else {
            if ($type == 'nvl') {
                $_type = 'materials';
                $__type = 'materials';
            } else {
                if ($type == 'tools') {
                    $_type = 'tools_supplies';
                    $__type = 'tools_supplies';
                }
            }
        }
        //Nhap
        // $CI->db->select_sum('quantity_net');
        // $CI->db->join('tblimport', 'tblimport.id=tblimport_items.id_import', 'left');
        // if ($warehouse_id) {
        //  $CI->db->where('tblimport.warehouse_id', $warehouse_id);
        // }
        // if ($startDate) {
        //  $CI->db->where('tblimport.date <', $startDate);
        // }
        // if ($localtion) {
        //  $CI->db->where('tblimport_items.localtion_warehouses_id', $localtion);
        // }
        // $quantity_import = $CI->db->get_where(
        //  'tblimport_items',
        //  array('product_id' => $product_id, 'type' => $type)
        // )->row()->quantity_net;

        $CI->db->select('Sum(quantity_payment) as quantity_net,SUM(quantity_payment*price)  as price');
        $CI->db->join('tblimport', 'tblimport.id=tblimport_items.id_import', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tblimport.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblimport.warehouseman_date <', $startDate);
        }
        $CI->db->where('warehouseman_id != 0');

        $import = $CI->db->get_where(
            'tblimport_items',
            array('product_id' => $product_id, 'type' => $type)
        )->row();
        $quantity_import = $import->price;
        if ($quantity_import == 0) {
            $quantity_import = getpriceimportwarehouse($product_id, $type, 1) * $import->quantity_net;
        }

        // -------------------------------
        // xuất kho
        $CI->db->select('tbl_delivery_items.*');
        $CI->db->join('tbl_deliveries', 'tbl_deliveries.id=tbl_delivery_items.delivery_id', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbl_delivery_items.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_deliveries.date_warehouseman <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbl_delivery_items.location_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_export = $CI->db->get_where(
            'tbl_delivery_items',
            array('item_id' => $product_id, 'type_item' => $_type)
        )->result_array();
        foreach ($quantity_export as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity += $quantitys * $price;
                }
            }
        }
        // ------------------------------
        // trả hàng ncc
        $CI->db->select('tblreturn_suppliers_items.*');
        $CI->db->join('tblreturn_suppliers', 'tblreturn_suppliers.id=tblreturn_suppliers_items.id_return', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tblreturn_suppliers.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblreturn_suppliers.data_warehouseman <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tblreturn_suppliers_items.localtion_warehouses_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_return = $CI->db->get_where(
            'tblreturn_suppliers_items',
            array('product_id' => $product_id, 'type' => $type)
        )->result_array();
        foreach ($quantity_return as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity += $quantitys * $price;
                }
            }
        }
        //xuat khac
        $CI->db->select('tbltblexport_different_items.*,tblexport_different.id as idd,tblexport_different.warehouseman_date');
        $CI->db->join('tblexport_different', 'tblexport_different.id=tbltblexport_different_items.id_export_different', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbltblexport_different_items.warehouses_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblexport_different.warehouseman_date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbltblexport_different_items.localtion_warehouses_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_adju_G = $CI->db->get_where(
            'tbltblexport_different_items',
            array('product_id' => $product_id, 'tbltblexport_different_items.type' => $type)
        )->result_array();

        foreach ($quantity_adju_G as $key => $value) {
            if (!empty($value['id_import'])) {
                $itemss = $value['id_import'];
                $array = explode('|', $itemss);
                foreach ($array as $ka => $va) {
                    if (!empty($va)) {
                        $waretos = explode('-', $va);
                        $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                        $price = $quantity_nets->price;
                        if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                            $quantitys = $waretos[4];
                        } else {
                            $quantitys = $waretos[3];
                        }
                        if (empty($quantitys) && ((($quantitys) != 0) || (($quantitys) == ''))) {
                            $quantitys = $value['quantity_payment'];
                        }
                        if ($price == 0) {
                            $price = getpriceimport($product_id, $type);
                        }
                        $exists_quantity += $quantitys * $price;
                    }
                }
            } else {
                $quantity_nets = get_table_where('tblwarehouse_product', array('product_id' => $value['product_id'], 'type_items' => $value['type'], 'localtion' => $value['localtion_warehouses_id'], 'date_warehouse <=' => $value['warehouseman_date']), 'date_warehouse DESC', 'row');
                if (!empty($quantity_nets)) {
                    $exists_quantity += $value['quantity_payment'] * $quantity_nets->price;
                } else {
                    $exists_quantity += 0;
                }
            }
        }

        // ------------------------------
        // điều chỉnh tăng
        if ($type == 'product') {
            $CI->db->select('Sum(quantity_net) as quantity_net,SUM(quantity_net*price)  as price');
        } else {
            $CI->db->select('Sum(quantity_payment) as quantity_net,SUM(quantity_payment*price)  as price');
        }
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbladjusted.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbladjusted.date_create <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbladjusted_items.localtion', $localtion);
        }
        $CI->db->where('tbladjusted.type', 1);
        $_adju_T = $CI->db->get_where(
            'tbladjusted_items',
            array('product_id' => $product_id, 'tbladjusted_items.type' => $type)
        )->row();
        $quantity_adju_T = $_adju_T->price;
        if ($quantity_adju_T == 0) {
            $quantity_adju_T = getpriceimportwarehouse($product_id, $type, 3) * $_adju_T->quantity_net;
        }
        // ------------------------------
        // điều chỉnh giảm
        $CI->db->select('tbladjusted_items.*');
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbladjusted.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbladjusted.date_create <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbladjusted_items.localtion', $localtion);
        }
        $CI->db->where('tbladjusted.type', 2);
        $quantity_adju_G = $CI->db->get_where(
            'tbladjusted_items',
            array('product_id' => $product_id, 'tbladjusted_items.type' => $type)
        )->result_array();

        foreach ($quantity_adju_G as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity += $quantitys * $price;
                }
            }
        }

        // ------------------------------
        // chuyển kho nhận
        // $CI->db->select('Sum(quantity_net) as quantity_net,SUM(quantity_net*price)  as price');
        // $CI->db->join(
        //  'tbltransfer_warehouse',
        //  'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer',
        //  'left'
        // );
        // if ($warehouse_id) {
        //  $CI->db->where('tbltransfer_warehouse_detail.warehouses_to', $warehouse_id);
        // }
        // if ($startDate) {
        //  $CI->db->where('tbltransfer_warehouse.date <', $startDate);
        // }
        // if ($localtion) {
        //  $CI->db->where('tbltransfer_warehouse_detail.localtion_to', $localtion);
        // }
        // $CI->db->where('warehouseman_id != 0');
        // $quantity_tranfer_N = $CI->db->get_where(
        //  'tbltransfer_warehouse_detail',
        //  array('id_items' => $product_id, 'type' => $type)
        // )->row()->price;

        // tien chuyen gia nhan
        $quantity_tranfer_N = 0;
        $CI->db->select('tbltransfer_warehouse_detail.*,tbltransfer_warehouse.id as idd');
        $CI->db->join(
            'tbltransfer_warehouse',
            'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where_in('tbltransfer_warehouse_detail.warehouses_to', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbltransfer_warehouse.warehouseman_date <', $startDate);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_tranfer_D = $CI->db->get_where(
            'tbltransfer_warehouse_detail',
            array('id_items' => $product_id, 'type' => $type)
        )->result_array();

        foreach ($quantity_tranfer_D as $key => $value) {
            $quantity_nets = get_table_where('tblwarehouse_product', array('import_id' => $value['idd'], 'type_export' => 2, 'product_id' => $value['id_items'], 'product_id' => $value['id_items'], 'type_items' => $value['type'], 'localtion' => $value['localtion_to']));

            foreach ($quantity_nets as $ka => $vs) {
                if ($vs['price'] == 0) {
                    $vs['price'] = getpriceimportwarehouse($product_id, $type, 2);
                }
                $quantity_tranfer_N += $vs['product_quantity_payment'] * $vs['price'];
            }
        }
        // foreach ($quantity_tranfer_D as $key => $value) {
        //  $itemss = $value['id_import'];
        //  $array = explode('|', $itemss);
        //  foreach ($array as $ka => $va) {
        //      if (!empty($va)) {
        //          $waretos = explode('-', $va);
        //          $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
        //          $price = $quantity_nets->price;
        //          $quantitys = $waretos[1];
        //          $quantity_tranfer_N += $quantitys * $price;
        //      }
        //  }
        // }
        // ------------------------------
        // chuyển kho đi
        $CI->db->select('tbltransfer_warehouse_detail.*,tbltransfer_warehouse.id as idd,tbltransfer_warehouse.warehouseman_date');
        $CI->db->join(
            'tbltransfer_warehouse',
            'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where_in('tbltransfer_warehouse_detail.warehouses_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbltransfer_warehouse.warehouseman_date <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbltransfer_warehouse_detail.localtion_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_tranfer_D = $CI->db->get_where(
            'tbltransfer_warehouse_detail',
            array('id_items' => $product_id, 'type' => $type)
        )->result_array();

        foreach ($quantity_tranfer_D as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');

                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity += $quantitys * $price;
                }
            }
        }

        // foreach ($quantity_tranfer_D as $key => $value) {

        //  $quantity_nets = get_table_where('tblwarehouse_product', array('product_id' => $value['id_items'], 'product_id' => $value['id_items'], 'type_items' => $value['type'], 'localtion' => $value['localtion_id'], 'date_warehouse <=' => $value['warehouseman_date']), 'date_warehouse DESC', 'row');
        //  if (!empty($quantity_nets)) {
        //      $exists_quantity += $value['quantity_net']*$quantity_nets->price;
        //  } else {
        //      $exists_quantity += 0;
        //  }


        //  // $quantity_nets = get_table_where('tblwarehouse_product', array('import_id' => $value['idd'], 'type_export' => 2, 'product_id' => $value['id_items'], 'product_id' => $value['id_items'], 'type_items' => $value['type'], 'localtion' => $value['localtion_id']), '', 'row');
        //  // if (!empty($quantity_nets)) {
        //  //  $exists_quantity += $value['quantity_net']*$quantity_nets->price;
        //  // } else {
        //  //  $exists_quantity += 0;
        //  // }
        //  // $itemss = $value['id_import'];
        //  // $array = explode('|', $itemss);
        //  // foreach ($array as $ka => $va) {
        //  //  if (!empty($va)) {
        //  //      $waretos = explode('-', $va);
        //  //      $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
        //  //      $price = $quantity_nets->price;
        //  //      $quantitys = $waretos[1];
        //  //      $exists_quantity += $quantitys * $price;
        //  //  }
        //  // }
        // }
        // ------------------------------
        // xuất kho sản xuất
        $CI->db->select('tbl_suggest_exporting_items.*');
        $CI->db->join(
            'tbl_suggest_exporting',
            'tbl_suggest_exporting.id=tbl_suggest_exporting_items.suggest_exporting_id',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where_in('tbl_suggest_exporting_items.warehouse_item_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_suggest_exporting.date_warehouseman <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbl_suggest_exporting_items.location_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');
        $CI->db->like('type_item', $_type);
        $quantity_purchase_products = $CI->db->get_where(
            'tbl_suggest_exporting_items',
            array('item_id' => $product_id)
        )->result_array();
        foreach ($quantity_purchase_products as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity += $quantitys * $price;
                }
            }
        }
        // ------------------------------
        // nhập kho thành phẩm
        // $CI->db->select_sum('quantity');
        $CI->db->select('Sum(quantity) as quantity,SUM(quantity*price)  as price');
        $CI->db->join(
            'tbl_purchase_products',
            'tbl_purchase_products.id=tbl_purchase_product_items.purchase_product_id',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where_in('tbl_purchase_products.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_purchase_products.date_warehouseman <', $startDate);
        }
        if ($localtion) {
            $CI->db->where('tbl_purchase_product_items.location_id', $localtion);
        }
        $CI->db->where('warehouseman_id != 0');

        $CI->db->like('tbl_purchase_product_items.type_item', $__type);

        $purchase_products = $CI->db->get_where(
            'tbl_purchase_product_items',
            array('item_id' => $product_id)
        )->row();

        $quantity_purchase_products = $purchase_products->price;
        if ($quantity_purchase_products == 0) {
            $quantity_purchase_products = getpriceimport($product_id, $type) * $purchase_products->quantity;
        }

        $CI->db->select('Sum(quantity) as quantity,SUM(quantity_payment*price)  as price');
        $CI->db->join(
            'tbl_purchase_internal',
            'tbl_purchase_internal.id=tbl_purchase_internal_items.purchase_internal_id',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where_in('tbl_purchase_internal.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate)) {
            $CI->db->where('tbl_purchase_internal.date_warehouseman <', $startDate);
        }
        $CI->db->where('warehouseman_id != 0');

        $CI->db->like('tbl_purchase_internal_items.type_item', $_type);

        $purchase_internal = $CI->db->get_where(
            'tbl_purchase_internal_items',
            array('item_id' => $product_id)
        )->row();
        $quantity_purchase_internal = $purchase_internal->price;
        if ($quantity_purchase_internal == 0) {
            $quantity_purchase_internal = getpriceimportwarehouse($product_id, $type, 20) * 0;
        }

        // ------------------------------
        $exists_quantity = $quantity_import + $quantity_adju_T + $quantity_tranfer_N + $quantity_purchase_products - $exists_quantity + $quantity_purchase_internal;
    }

    return $exists_quantity;
}
function getStartInventory_trongkiArray($product_id = null, $type = null, $warehouse_id = null, $startDate = null, $startEnd = null)
{
    $CI = &get_instance();
    $exists_quantity_import = 0;
    $exists_quantity_export = 0;
    $data['exists_quantity_import'] = $exists_quantity_import;
    $data['exists_quantity_export'] = $exists_quantity_export;
    if ($startDate) {
        $startDate = $startDate . ' 00:00:00';
    }
    if ($startEnd) {
        $startEnd = $startEnd . ' 23:59:59';
    }
    if (is_numeric($product_id) && !empty($type)) {
        if ($type == 'product') {
            $_type = 'products';
            $__type = 'product';
        } else {
            if ($type == 'nvl') {
                $_type = 'materials';
                $__type = 'materials';
            } else {
                if ($type == 'tools') {
                    $_type = 'tools_supplies';
                    $__type = 'tools_supplies';
                }
            }
        }
        //Nhap
        $CI->db->select('Sum(quantity_payment) as quantity_net,SUM(quantity_payment*price)  as price');
        $CI->db->join('tblimport', 'tblimport.id=tblimport_items.id_import', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tblimport.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblimport.warehouseman_date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tblimport.warehouseman_date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');

        $import = $CI->db->get_where(
            'tblimport_items',
            array('product_id' => $product_id, 'type' => $type)
        )->row();
        $quantity_import = $import->price;
        if ($quantity_import == 0) {
            $quantity_import = getpriceimportwarehouse($product_id, $type, 1) * $import->quantity_net;
        }

        $exists_quantity_import += $quantity_import;
        // -------------------------------
        // xuất kho
        $CI->db->select('tbl_delivery_items.*');
        $CI->db->join('tbl_deliveries', 'tbl_deliveries.id=tbl_delivery_items.delivery_id', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbl_delivery_items.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_deliveries.date_warehouseman >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbl_deliveries.date_warehouseman <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_export = $CI->db->get_where(
            'tbl_delivery_items',
            array('item_id' => $product_id, 'type_item' => $_type)
        )->result_array();
        foreach ($quantity_export as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity_export += $quantitys * $price;
                }
            }
        }

        // ------------------------------
        // trả hàng ncc
        $CI->db->select('tblreturn_suppliers_items.*');
        $CI->db->join('tblreturn_suppliers', 'tblreturn_suppliers.id=tblreturn_suppliers_items.id_return', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tblreturn_suppliers.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblreturn_suppliers.data_warehouseman >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tblreturn_suppliers.data_warehouseman <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_return = $CI->db->get_where(
            'tblreturn_suppliers_items',
            array('product_id' => $product_id, 'type' => $type)
        )->result_array();
        foreach ($quantity_return as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity_export += $quantitys * $price;
                }
            }
        }
        //xuat khac
        $CI->db->select('tbltblexport_different_items.*,tblexport_different.id as idd,tblexport_different.warehouseman_date');
        $CI->db->join('tblexport_different', 'tblexport_different.id=tbltblexport_different_items.id_export_different', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbltblexport_different_items.warehouses_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblexport_different.warehouseman_date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tblexport_different.warehouseman_date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_adju_G = $CI->db->get_where(
            'tbltblexport_different_items',
            array('product_id' => $product_id, 'tbltblexport_different_items.type' => $type)
        )->result_array();
        foreach ($quantity_adju_G as $key => $value) {
            if (!empty($value['id_import'])) {
                $itemss = $value['id_import'];
                $array = explode('|', $itemss);
                foreach ($array as $ka => $va) {
                    if (!empty($va)) {
                        $waretos = explode('-', $va);
                        $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                        $price = $quantity_nets->price;

                        if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                            $quantitys = $waretos[4];
                        } else {
                            $quantitys = $waretos[3];
                        }

                        if (empty($quantitys) && ((($quantitys) != 0) || (($quantitys) == ''))) {
                            $quantitys = $value['quantity_payment'];
                        }

                        if ($price == 0) {
                            $price = getpriceimport($product_id, $type);
                        }

                        $exists_quantity_export += $quantitys * $price;
                    }
                }
            } else {
                $quantity_nets = get_table_where('tblwarehouse_product', array('product_id' => $value['product_id'], 'type_items' => $value['type'], 'localtion' => $value['localtion_warehouses_id'], 'date_warehouse <=' => $value['warehouseman_date']), 'date_warehouse DESC', 'row');
                if (!empty($quantity_nets)) {
                    $exists_quantity_export += $value['quantity_payment'] * $quantity_nets->price;
                } else {
                    $exists_quantity_export += 0;
                }
            }
        }

        // ------------------------------
        // điều chỉnh tăng
        if ($type == 'product') {
            $CI->db->select('Sum(quantity_net) as quantity_net,SUM(quantity_net*price)  as price');
        } else {
            $CI->db->select('Sum(quantity_payment) as quantity_net,SUM(quantity_payment*price)  as price');
        }
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbladjusted.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbladjusted.date_create >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbladjusted.date_create <=', $startEnd);
        }
        $CI->db->where('tbladjusted.type', 1);
        $_adju_T = $CI->db->get_where(
            'tbladjusted_items',
            array('product_id' => $product_id, 'tbladjusted_items.type' => $type)
        )->row();
        $quantity_adju_T = $_adju_T->price;

        if ($quantity_adju_T == 0) {
            $quantity_adju_T = getpriceimportwarehouse($product_id, $type, 3) * $_adju_T->quantity_net;
        }

        $exists_quantity_import += $quantity_adju_T;

        // ------------------------------
        // điều chỉnh giảm
        $CI->db->select('tbladjusted_items.*');
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbladjusted.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbladjusted.date_create >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbladjusted.date_create <=', $startEnd);
        }
        $CI->db->where('tbladjusted.type', 2);
        $quantity_adju_G = $CI->db->get_where(
            'tbladjusted_items',
            array('product_id' => $product_id, 'tbladjusted_items.type' => $type)
        )->result_array();

        foreach ($quantity_adju_G as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity_export += $quantitys * $price;
                }
            }
        }

        // ------------------------------
        // chuyển kho nhận
        // tien chuyen gia nhan
        $quantity_tranfer_N = 0;
        $CI->db->select('tbltransfer_warehouse_detail.*,tbltransfer_warehouse.id as idd');
        $CI->db->join(
            'tbltransfer_warehouse',
            'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where_in('tbltransfer_warehouse_detail.warehouses_to', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbltransfer_warehouse.warehouseman_date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbltransfer_warehouse.warehouseman_date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_tranfer_D = $CI->db->get_where(
            'tbltransfer_warehouse_detail',
            array('id_items' => $product_id, 'type' => $type)
        )->result_array();
        foreach ($quantity_tranfer_D as $key => $value) {
            $quantity_nets = get_table_where('tblwarehouse_product', array('import_id' => $value['idd'], 'type_export' => 2, 'product_id' => $value['id_items'], 'product_id' => $value['id_items'], 'type_items' => $value['type'], 'localtion' => $value['localtion_to']));
            foreach ($quantity_nets as $ka => $vs) {
                if ($vs['price'] == 0) {
                    $vs['price'] = getpriceimportwarehouse($product_id, $type, 2);
                }
                $exists_quantity_import += $vs['product_quantity_payment'] * $vs['price'];
            }
        }

        // }
        // ------------------------------
        // chuyển kho đi
        $CI->db->select('tbltransfer_warehouse_detail.*,tbltransfer_warehouse.id as idd,tbltransfer_warehouse.warehouseman_date');
        $CI->db->join(
            'tbltransfer_warehouse',
            'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where_in('tbltransfer_warehouse_detail.warehouses_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbltransfer_warehouse.warehouseman_date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbltransfer_warehouse.warehouseman_date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_tranfer_D = $CI->db->get_where(
            'tbltransfer_warehouse_detail',
            array('id_items' => $product_id, 'type' => $type)
        )->result_array();

        foreach ($quantity_tranfer_D as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);

                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    // $quantitys = $waretos[1];
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($quantitys > $value['quantity_payment']) {
                        $quantitys = $value['quantity_payment'];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity_export += $quantitys * $price;
                }
            }
        }

        // xuất kho sản xuất
        $CI->db->select('tbl_suggest_exporting_items.*');
        $CI->db->join(
            'tbl_suggest_exporting',
            'tbl_suggest_exporting.id=tbl_suggest_exporting_items.suggest_exporting_id',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where_in('tbl_suggest_exporting_items.warehouse_item_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_suggest_exporting.date_warehouseman >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbl_suggest_exporting.date_warehouseman <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');
        $CI->db->like('type_item', $_type);
        $quantity_purchase_products = $CI->db->get_where(
            'tbl_suggest_exporting_items',
            array('item_id' => $product_id)
        )->result_array();
        foreach ($quantity_purchase_products as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($price == 0) {
                        $price = getpriceimport($product_id, $type);
                    }
                    $exists_quantity_export += $quantitys * $price;
                }
            }
        }

        // ------------------------------
        // nhập kho thành phẩm
        // $CI->db->select_sum('quantity');
        $CI->db->select('Sum(quantity) as quantity,SUM(quantity*price)  as price');
        $CI->db->join(
            'tbl_purchase_products',
            'tbl_purchase_products.id=tbl_purchase_product_items.purchase_product_id',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where_in('tbl_purchase_products.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_purchase_products.date_warehouseman >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbl_purchase_products.date_warehouseman <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');

        $CI->db->like('tbl_purchase_product_items.type_item', $__type);

        $purchase_products = $CI->db->get_where(
            'tbl_purchase_product_items',
            array('item_id' => $product_id)
        )->row();

        $quantity_purchase_products = $purchase_products->price;
        if ($quantity_purchase_products == 0) {
            $quantity_purchase_products = getpriceimport($product_id, $type) * 0;
        }
        $exists_quantity_import += $quantity_purchase_products;

        $CI->db->select('Sum(quantity) as quantity,SUM(quantity_payment*price)  as price');
        $CI->db->join(
            'tbl_purchase_internal',
            'tbl_purchase_internal.id=tbl_purchase_internal_items.purchase_internal_id',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where_in('tbl_purchase_internal.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbl_purchase_internal.date_warehouseman >=', $startDate . ' 00:00:00');
            $CI->db->where('tbl_purchase_internal.date_warehouseman <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');

        $CI->db->like('tbl_purchase_internal_items.type_item', $_type);

        $purchase_internal = $CI->db->get_where(
            'tbl_purchase_internal_items',
            array('item_id' => $product_id)
        )->row();
        $quantity_purchase_internal = $purchase_internal->price;
        if ($quantity_purchase_internal == 0) {
            $quantity_purchase_internal = getpriceimportwarehouse($product_id, $type, 20) * 0;
        }
        $exists_quantity_import += $quantity_purchase_internal;

        // ------------------------------
        $data['exists_quantity_import'] = $exists_quantity_import;
        $data['exists_quantity_export'] = $exists_quantity_export;

        // $exists_quantity = $quantity_import  + $quantity_adju_T + $quantity_tranfer_N + $quantity_purchase_products - $exists_quantity + $quantity_purchase_internal;
    }
    return $data;
}
function getStartInventory_trongkiArray_dashbo($product_id = null, $type = null, $warehouse_id = null, $startDate = null, $startEnd = null)
{
    $CI = &get_instance();
    $exists_quantity_import = 0;
    $exists_quantity_export = 0;
    $data['exists_quantity_import'] = $exists_quantity_import;
    $data['exists_quantity_export'] = $exists_quantity_export;
    if ($startDate) {
        $startDate = $startDate . ' 00:00:00';
    }
    if ($startEnd) {
        $startEnd = $startEnd . ' 23:59:59';
    }
    if (is_numeric($product_id) && !empty($type)) {
        if ($type == 'product') {
            $_type = 'products';
            $__type = 'product';
        } else {
            if ($type == 'nvl') {
                $_type = 'materials';
                $__type = 'materials';
            } else {
                if ($type == 'tools') {
                    $_type = 'tools_supplies';
                    $__type = 'tools_supplies';
                }
            }
        }
        //Nhap
        $CI->db->select('Sum(quantity_payment) as quantity_net,SUM(quantity_payment*price)  as price');
        $CI->db->join('tblimport', 'tblimport.id=tblimport_items.id_import', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tblimport.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblimport.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tblimport.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');

        $import = $CI->db->get_where(
            'tblimport_items',
            array('product_id' => $product_id, 'type' => $type)
        )->row();
        $quantity_import = $import->price;
        $quantity_import = getpriceimport($product_id, $type) * $import->quantity_net;


        $exists_quantity_import += $quantity_import;
        // -------------------------------
        // xuất kho
        $CI->db->select('tbl_delivery_items.*');
        $CI->db->join('tbl_deliveries', 'tbl_deliveries.id=tbl_delivery_items.delivery_id', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbl_delivery_items.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_deliveries.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbl_deliveries.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_export = $CI->db->get_where(
            'tbl_delivery_items',
            array('item_id' => $product_id, 'type_item' => $_type)
        )->result_array();
        foreach ($quantity_export as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    $price = getpriceimport($product_id, $type);

                    $exists_quantity_export += $quantitys * $price;
                }
            }
        }

        // ------------------------------
        // trả hàng ncc
        $CI->db->select('tblreturn_suppliers_items.*');
        $CI->db->join('tblreturn_suppliers', 'tblreturn_suppliers.id=tblreturn_suppliers_items.id_return', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tblreturn_suppliers.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblreturn_suppliers.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tblreturn_suppliers.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_return = $CI->db->get_where(
            'tblreturn_suppliers_items',
            array('product_id' => $product_id, 'type' => $type)
        )->result_array();
        foreach ($quantity_return as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    $price = getpriceimport($product_id, $type);

                    $exists_quantity_export += $quantitys * $price;
                }
            }
        }
        //xuat khac
        $CI->db->select('tbltblexport_different_items.*,tblexport_different.id as idd,tblexport_different.warehouseman_date');
        $CI->db->join('tblexport_different', 'tblexport_different.id=tbltblexport_different_items.id_export_different', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbltblexport_different_items.warehouses_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tblexport_different.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tblexport_different.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');

        $quantity_adju_G = $CI->db->get_where(
            'tbltblexport_different_items',
            array('product_id' => $product_id, 'tbltblexport_different_items.type' => $type)
        )->result_array();
        foreach ($quantity_adju_G as $key => $value) {
            if (!empty($value['id_import'])) {
                $itemss = $value['id_import'];
                $array = explode('|', $itemss);
                foreach ($array as $ka => $va) {
                    if (!empty($va)) {
                        $waretos = explode('-', $va);
                        $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                        $price = $quantity_nets->price;

                        if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                            $quantitys = $waretos[4];
                        } else {
                            $quantitys = $waretos[3];
                        }

                        if (empty($quantitys) && ((($quantitys) != 0) || (($quantitys) == ''))) {
                            $quantitys = $value['quantity_payment'];
                        }

                        $price = getpriceimport($product_id, $type);


                        $exists_quantity_export += $quantitys * $price;
                    }
                }
            } else {
                $quantity_nets = get_table_where('tblwarehouse_product', array('product_id' => $value['product_id'], 'type_items' => $value['type'], 'localtion' => $value['localtion_warehouses_id'], 'date_warehouse <=' => $value['warehouseman_date']), 'date_warehouse DESC', 'row');
                if (!empty($quantity_nets)) {
                    $exists_quantity_export += $value['quantity_payment'] * $quantity_nets->price;
                } else {
                    $exists_quantity_export += 0;
                }
            }
        }

        // ------------------------------
        // điều chỉnh tăng
        if ($type == 'product') {
            $CI->db->select('Sum(quantity_net) as quantity_net,SUM(quantity_net*price)  as price');
        } else {
            $CI->db->select('Sum(quantity_payment) as quantity_net,SUM(quantity_payment*price)  as price');
        }
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbladjusted.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbladjusted.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbladjusted.date <=', $startEnd);
        }
        $CI->db->where('tbladjusted.type', 1);
        $_adju_T = $CI->db->get_where(
            'tbladjusted_items',
            array('product_id' => $product_id, 'tbladjusted_items.type' => $type)
        )->row();
        $quantity_adju_T = $_adju_T->price;

        $quantity_adju_T = getpriceimport($product_id, $type) * $_adju_T->quantity_net;


        $exists_quantity_import += $quantity_adju_T;

        // ------------------------------
        // điều chỉnh giảm
        $CI->db->select('tbladjusted_items.*');
        $CI->db->join('tbladjusted', 'tbladjusted.id=tbladjusted_items.id_adjusted', 'left');
        if ($warehouse_id) {
            $CI->db->where_in('tbladjusted.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbladjusted.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbladjusted.date <=', $startEnd);
        }
        $CI->db->where('tbladjusted.type', 2);
        $quantity_adju_G = $CI->db->get_where(
            'tbladjusted_items',
            array('product_id' => $product_id, 'tbladjusted_items.type' => $type)
        )->result_array();

        foreach ($quantity_adju_G as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    $price = getpriceimport($product_id, $type);

                    $exists_quantity_export += $quantitys * $price;
                }
            }
        }

        // ------------------------------
        // chuyển kho nhận
        // tien chuyen gia nhan
        $quantity_tranfer_N = 0;
        $CI->db->select('tbltransfer_warehouse_detail.*,tbltransfer_warehouse.id as idd');
        $CI->db->join(
            'tbltransfer_warehouse',
            'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where_in('tbltransfer_warehouse_detail.warehouses_to', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbltransfer_warehouse.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbltransfer_warehouse.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_tranfer_D = $CI->db->get_where(
            'tbltransfer_warehouse_detail',
            array('id_items' => $product_id, 'type' => $type)
        )->result_array();
        foreach ($quantity_tranfer_D as $key => $value) {
            $quantity_nets = get_table_where('tblwarehouse_product', array('import_id' => $value['idd'], 'type_export' => 2, 'product_id' => $value['id_items'], 'product_id' => $value['id_items'], 'type_items' => $value['type'], 'localtion' => $value['localtion_to']));
            foreach ($quantity_nets as $ka => $vs) {
                $vs['price'] = getpriceimport($product_id, $type);

                $exists_quantity_import += $vs['product_quantity_payment'] * $vs['price'];
            }
        }

        // }
        // ------------------------------
        // chuyển kho đi
        $CI->db->select('tbltransfer_warehouse_detail.*,tbltransfer_warehouse.id as idd,tbltransfer_warehouse.warehouseman_date');
        $CI->db->join(
            'tbltransfer_warehouse',
            'tbltransfer_warehouse.id=tbltransfer_warehouse_detail.id_transfer',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where_in('tbltransfer_warehouse_detail.warehouses_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbltransfer_warehouse.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbltransfer_warehouse.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');
        $quantity_tranfer_D = $CI->db->get_where(
            'tbltransfer_warehouse_detail',
            array('id_items' => $product_id, 'type' => $type)
        )->result_array();

        foreach ($quantity_tranfer_D as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);

                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    // $quantitys = $waretos[1];
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    if ($quantitys > $value['quantity_payment']) {
                        $quantitys = $value['quantity_payment'];
                    }
                    $price = getpriceimport($product_id, $type);

                    $exists_quantity_export += $quantitys * $price;
                }
            }
        }

        // xuất kho sản xuất
        $CI->db->select('tbl_suggest_exporting_items.*');
        $CI->db->join(
            'tbl_suggest_exporting',
            'tbl_suggest_exporting.id=tbl_suggest_exporting_items.suggest_exporting_id',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where_in('tbl_suggest_exporting_items.warehouse_item_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_suggest_exporting.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbl_suggest_exporting.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');
        $CI->db->like('type_item', $_type);
        $quantity_purchase_products = $CI->db->get_where(
            'tbl_suggest_exporting_items',
            array('item_id' => $product_id)
        )->result_array();
        foreach ($quantity_purchase_products as $key => $value) {
            $itemss = $value['id_import'];
            $array = explode('|', $itemss);
            foreach ($array as $ka => $va) {
                if (!empty($va)) {
                    $waretos = explode('-', $va);
                    $quantity_nets = get_table_where('tblwarehouse_product', array('id' => $waretos[0]), '', 'row');
                    $price = $quantity_nets->price;
                    if (empty($waretos[3]) && ((($waretos[3]) != 0) || (($waretos[3]) == ''))) {
                        $quantitys = $waretos[4];
                    } else {
                        $quantitys = $waretos[3];
                    }
                    $price = getpriceimport($product_id, $type);

                    $exists_quantity_export += $quantitys * $price;
                }
            }
        }

        // ------------------------------
        // nhập kho thành phẩm
        // $CI->db->select_sum('quantity');
        $CI->db->select('Sum(quantity) as quantity,SUM(quantity*price)  as price');
        $CI->db->join(
            'tbl_purchase_products',
            'tbl_purchase_products.id=tbl_purchase_product_items.purchase_product_id',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where_in('tbl_purchase_products.warehouse_id', $warehouse_id);
        }
        if ($startDate) {
            $CI->db->where('tbl_purchase_products.date >=', $startDate);
        }
        if ($startEnd) {
            $CI->db->where('tbl_purchase_products.date <=', $startEnd);
        }
        $CI->db->where('warehouseman_id != 0');

        $CI->db->like('tbl_purchase_product_items.type_item', $__type);

        $purchase_products = $CI->db->get_where(
            'tbl_purchase_product_items',
            array('item_id' => $product_id)
        )->row();

        $quantity_purchase_products = $purchase_products->price;
        if ($quantity_purchase_products == 0) {
            $quantity_purchase_products = getpriceimport($product_id, $type) * 0;
        }
        $exists_quantity_import += $quantity_purchase_products;

        $CI->db->select('Sum(quantity) as quantity,SUM(quantity_payment*price)  as price');
        $CI->db->join(
            'tbl_purchase_internal',
            'tbl_purchase_internal.id=tbl_purchase_internal_items.purchase_internal_id',
            'left'
        );
        if ($warehouse_id) {
            $CI->db->where_in('tbl_purchase_internal.warehouse_id', $warehouse_id);
        }
        if (!empty($startDate) && !empty($startEnd)) {
            $CI->db->where('tbl_purchase_internal.date >=', $startDate . ' 00:00:00');
            $CI->db->where('tbl_purchase_internal.date <=', $startEnd . ' 23:59:59');
        }
        $CI->db->where('warehouseman_id != 0');

        $CI->db->like('tbl_purchase_internal_items.type_item', $_type);

        $purchase_internal = $CI->db->get_where(
            'tbl_purchase_internal_items',
            array('item_id' => $product_id)
        )->row();
        $quantity_purchase_internal = $purchase_internal->price;
        if ($quantity_purchase_internal == 0) {
            $quantity_purchase_internal = getpriceimportwarehouse($product_id, $type, 20) * 0;
        }
        $exists_quantity_import += $quantity_purchase_internal;

        // ------------------------------
        $data['exists_quantity_import'] = $exists_quantity_import;
        $data['exists_quantity_export'] = $exists_quantity_export;

        // $exists_quantity = $quantity_import  + $quantity_adju_T + $quantity_tranfer_N + $quantity_purchase_products - $exists_quantity + $quantity_purchase_internal;
    }
    return $data;
}
function get_full_item_export($id = '', $type = '')
{
    $CI = &get_instance();
    if ($type == 'items') {
        $CI->db->select('tblitems.*')->distinct();
        $CI->db->from('tblitems');
        $CI->db->order_by('tblitems.id', 'desc');
        if (is_numeric($id)) {
            $CI->db->where('tblitems.id', $id);
            $item = $CI->db->get()->row();
            return $item;
        }
    } else {
        $table = get_table_where('tbltype_items', array('type' => $type), '', 'row')->table;
        $CI->db->select($table . '.*,' . $table . '.images as avatar')->distinct();
        $CI->db->from($table);
        $CI->db->order_by($table . '.id', 'desc');
        if (is_numeric($id)) {
            $CI->db->where($table . '.id', $id);
            $item = $CI->db->get()->row();
            return $item;
        }
    }
}

function getLocationLot($where = array(), $items = NULL, $checked = '', $warehouse = '', $type = '')
{
    $CI = &get_instance();
    $CI->db->where('status', 0);

    $CI->db->where('(( child = 1 and id_parent = 0) or ((id_parent is null or id_parent = 0) and child = 0))');

    if (!empty($where)) {
        $CI->db->where($where);
    }
    $localtion = $CI->db->get('tbllocaltion_warehouses')->result_array();
    $string_option = "<option></option>";
    foreach ($localtion as $key => $value) {
        if (!empty($value['id'])) {
            $checkeds = '';
            if ($checked == $value['id']) {
                $checkeds = 'selected';
                $value['child'] = 1;
            }
            $quantity_net = 0;
            $arrWarehouse_items = get_table_where('tblwarehouse_items', array('warehouse_id' => $warehouse, 'id_items' => $items, 'type_items' => $type, 'localtion' => $value['id'], 'product_quantity > ' => 0), '', 'result_array');
            foreach ($arrWarehouse_items as $warehouse_items) {
                if (!empty($warehouse_items)) {
                    $quantity_net = $warehouse_items['product_quantity'];
                }
                $optionValue = $warehouse . '__' . $value['id'] . "__" . $warehouse_items['lot_code'] . "__" . $warehouse_items['date_sx'] . "__" . $warehouse_items['date_sd'] . "__" . $warehouse_items['date_use'];
                $optionText = $value['name'] . ' - Lot: ' . $warehouse_items['lot_code'] . ' - Ngày SX: ' . $warehouse_items['date_sx'] . ' - Ngày SD: ' . $warehouse_items['date_sd'];

                $string_option .= '<option quantity-data=' . $quantity_net . ' ' . $checkeds . ' value="' . $optionValue . '" ' . ($value['child'] ? 'child="' . $value['child'] . '"' : '') . ' data-content="' . $value['name_parent'] . '" content="' . $value['name_parent'] . '">' . $optionText . '</option>';
                if (empty($lever)) {
                    $string_option .= option_child_localtion_warehouses_return($value['id'], '', $checked, $items, $warehouse, $type);
                }
            }
        }
    }
    return $string_option;
}
function update_plan_propose_internal_proposal($id = '')
{
    $CI = &get_instance();
    $CI->db->where('tblother_payslips_detail.id_service', $id);
    $CI->db->where('tblother_payslips.type_vouchers', 12);
    $CI->db->join('tblother_payslips', 'tblother_payslips.id = tblother_payslips_detail.other_pay', 'LEFT');
    $other_payslips = $CI->db->get('tblother_payslips_detail')->row();
    $internal_proposal = get_table_where('tblinternal_proposal', array('id_suggestion' => $id));
    foreach ($internal_proposal as $key => $value) {
        $plan_propose = get_table_where('tblplan_propose', array('id_internal_proposal' => $value['id'], 'type_plan_propose' => 'pay_slip'));
        foreach ($plan_propose as $k => $v) {
            $staff = get_staff_user_id();
            if (!empty($other_payslips)) {
                $data = [
                    'approved_by' => $staff,
                    'status' => 1,
                    'reason' => NULL,
                ];
            } else {
                $data = [
                    'approved_by' => NULL,
                    'status' => 0,
                    'reason' => NULL,
                ];
            }
            $CI->db->where('id', $v['id']);
            $success = $CI->db->update('plan_propose', $data);
        }
    }
}
function countTrouble($id = '', $date = '')
{
    $CI = &get_instance();
    $CI->db->from('tblproduction_report');
    $CI->db->where('tblproduction_report.id_trouble', $id);
    $CI->db->where('MONTH(tblproduction_report.date)', $date);
    $count = $CI->db->get()->num_rows() + 1;
    return ($count);
}
function countViolate($id = '', $date_start = '', $date_end = '')
{
    $CI = &get_instance();
    $CI->db->from('tblproduction_report');
    $CI->db->where('tblproduction_report.staff_responsible', $id);
    $CI->db->where('tblproduction_report.violate', 1);
    $CI->db->where('tblproduction_report.date >=', $date_start);
    $CI->db->where('tblproduction_report.date <=', $date_end);
    $count = $CI->db->get()->num_rows() + 1;
    return ($count);
}

function getQuarterStartAndEndDate($date)
{
    // Chuyển đổi ngày vào định dạng DateTime
    $date = new DateTime($date);

    // Lấy năm và tháng từ ngày
    $year = $date->format('Y');
    $month = $date->format('m');

    // Xác định quý
    if ($month >= 1 && $month <= 3) {
        $start = new DateTime("$year-01-01");
        $end = new DateTime("$year-03-31");
    } elseif ($month >= 4 && $month <= 6) {
        $start = new DateTime("$year-04-01");
        $end = new DateTime("$year-06-30");
    } elseif ($month >= 7 && $month <= 9) {
        $start = new DateTime("$year-07-01");
        $end = new DateTime("$year-09-30");
    } else {
        $start = new DateTime("$year-10-01");
        $end = new DateTime("$year-12-31");
    }

    // Trả về ngày bắt đầu và kết thúc của quý
    return [
        'start' => $start->format('Y-m-d'),
        'end' => $end->format('Y-m-d')
    ];
}
function parseIdImportString($s, $mode = 'default', $quantity_payment = 0)
{
    $out = [];
    if (!$s)
        return $out;

    $chunks = explode('|', $s);
    foreach ($chunks as $seg) {
        $seg = trim($seg);
        if ($seg === '')
            continue;

        $parts = explode('-', $seg);
        $wpId = isset($parts[0]) ? (int) $parts[0] : 0;
        $qty = 0;

        // Chọn vị trí số lượng tùy loại
        if ($mode === 'transfer') {
            if (isset($parts[3]) && $parts[3] !== '') {
                $qty = (float) $parts[3];
            } elseif (isset($parts[4]) && $parts[4] !== '') {
                $qty = (float) $parts[4];
            }
            if ($qty > $quantity_payment) {
                $qty = $quantity_payment;
            }
        } else if ($mode === 'adjusted') {

            // Ví dụ chuyển kho → số lượng nằm ở index 1
            if (isset($parts[1]) && $parts[1] !== '') {
                $qty = (float) $parts[1];
            }
            if ($qty > $quantity_payment) {
                $qty = $quantity_payment;
            }
        } else if ($mode === 'suggest_exporting') {
            if (isset($parts[3]) && $parts[3] !== '') {
                $qty = (float) $parts[3];
            } elseif (isset($parts[4]) && $parts[4] !== '') {
                $qty = (float) $parts[4];
            }
            if ($qty > $quantity_payment) {
                $qty = $quantity_payment;
            }
        } else if ($mode === 'export_different') {
            if (isset($parts[3]) && $parts[3] !== '') {
                $qty = (float) $parts[3];
            } elseif (isset($parts[4]) && $parts[4] !== '') {
                $qty = (float) $parts[4];
            }
            if ($qty > $quantity_payment) {
                $qty = $quantity_payment;
            }
            // if ($qty  == 0) {
            //     $qty = $quantity_payment;
            // }
        } else {
            // Mặc định: ưu tiên index 3 rồi index 4
            if (isset($parts[3]) && $parts[3] !== '') {
                $qty = (float) $parts[3];
            } elseif (isset($parts[4]) && $parts[4] !== '') {
                $qty = (float) $parts[4];
            }
        }

        if ($wpId > 0 && $qty != 0) {
            $out[] = ['wp_id' => $wpId, 'qty' => $qty];
        }
    }
    return $out;
}


/** Tải giá theo wp_id từ tblwarehouse_product, trả về map [wp_id => price] */
function loadWpPrices(array $wpIds)
{
    $wpIds = array_values(array_unique(array_filter($wpIds)));
    if (!$wpIds)
        return [];
    $CI = &get_instance();
    $CI->db->select('id, price');
    $CI->db->where_in('id', $wpIds);
    $rows = $CI->db->get('tblwarehouse_product')->result_array();
    $map = [];
    foreach ($rows as $r)
        $map[(int) $r['id']] = (float) $r['price'];
    return $map;
}
function fetchExportRowsBatch(array $itemsToCost, $type_itemss, $beginMonth, $endMonth, array $warehouseIds)
{
    $CI = &get_instance();
    $result = [
        'before' => [], // map [type][product_id] => list rows
        'during' => []
    ];
    $type_sql = ($type_itemss === 'product') ? 'products' : 'materials';

    // Helper: build where for item ids
    $filterItem = function ($tableItemId, $typeCol = null) use ($CI, $itemsToCost, $type_itemss, $type_sql) {
        if (!empty($itemsToCost[$type_itemss])) {
            $CI->db->where_in($tableItemId, $itemsToCost[$type_itemss]);
        }
        // if ($typeCol) {
        //     // một số bảng dùng 'type' = 'product'/'nvl', số khác dùng 'type_item' = 'products'/'materials'
        //     $CI->db->where($typeCol, in_array($typeCol, ['type']) ? $type_itemss : $type_sql);
        // }
        if ($typeCol) {
            $value = (preg_match('/(^|\.)(type)$/', $typeCol)) ? $type_itemss : $type_sql;
            $CI->db->where($typeCol, $value);
        }
    };

    // Helper: add warehouse filter
    $filterWh = function ($col) use ($CI, $warehouseIds) {
        if ($warehouseIds)
            $CI->db->where_in($col, $warehouseIds);
    };

    // ===== Delivey (xuất bán) =====
    // BEFORE
    $CI->db->select('di.item_id as product_id, di.id_import, d.date_warehouseman as dt, di.type_item, di.warehouse_id,CONCAT("default") as type_check');
    $CI->db->from('tbl_delivery_items di');
    $CI->db->join('tbl_deliveries d', 'd.id = di.delivery_id AND d.warehouseman_id != 0', 'inner');
    $filterItem('di.item_id', 'di.type_item');             // type_item = products/materials
    $filterWh('di.warehouse_id');
    $CI->db->where('d.date_warehouseman <', $beginMonth);
    $rows = $CI->db->get()->result_array();
    foreach ($rows as $r)
        $result['before'][$type_itemss][$r['product_id']][] = [
            'id_import' => $r['id_import'],
            'type_check' => $r['type_check'],
        ];

    // DURING
    $CI->db->select('di.item_id as product_id, di.id_import, d.date_warehouseman as dt, di.type_item, di.warehouse_id,CONCAT("default") as type_check');
    $CI->db->from('tbl_delivery_items di');
    $CI->db->join('tbl_deliveries d', 'd.id = di.delivery_id AND d.warehouseman_id != 0', 'inner');
    $filterItem('di.item_id', 'di.type_item');
    $filterWh('di.warehouse_id');
    $CI->db->where('d.date_warehouseman >=', $beginMonth);
    $CI->db->where('d.date_warehouseman <=', $endMonth);
    $rows = $CI->db->get()->result_array();
    foreach ($rows as $r)
        $result['during'][$type_itemss][$r['product_id']][] = [
            'id_import' => $r['id_import'],
            'type_check' => $r['type_check'],
        ];

    // ===== Return to supplier (trả NCC) =====
    // BEFORE
    $CI->db->select('rsi.product_id, rsi.id_import, rs.data_warehouseman as dt, rsi.type, rs.warehouse_id,CONCAT("default") as type_check');
    $CI->db->from('tblreturn_suppliers_items rsi');
    $CI->db->join('tblreturn_suppliers rs', 'rs.id = rsi.id_return AND rs.warehouseman_id != 0', 'inner');
    $filterItem('rsi.product_id', 'rsi.type');             // type = product/nvl
    $filterWh('rs.warehouse_id');
    $CI->db->where('rs.data_warehouseman <', $beginMonth);
    $rows = $CI->db->get()->result_array();
    foreach ($rows as $r)
        $result['before'][$type_itemss][$r['product_id']][] = [
            'id_import' => $r['id_import'],
            'type_check' => $r['type_check'],
        ];

    // DURING
    $CI->db->select('rsi.product_id, rsi.id_import, rs.data_warehouseman as dt, rsi.type, rs.warehouse_id,CONCAT("default") as type_check');
    $CI->db->from('tblreturn_suppliers_items rsi');
    $CI->db->join('tblreturn_suppliers rs', 'rs.id = rsi.id_return AND rs.warehouseman_id != 0', 'inner');
    $filterItem('rsi.product_id', 'rsi.type');
    $filterWh('rs.warehouse_id');
    $CI->db->where('rs.data_warehouseman >=', $beginMonth);
    $CI->db->where('rs.data_warehouseman <=', $endMonth);
    $rows = $CI->db->get()->result_array();
    foreach ($rows as $r)
        $result['during'][$type_itemss][$r['product_id']][] = [
            'id_import' => $r['id_import'],
            'type_check' => $r['type_check'],
        ];

    // ===== Export different (xuất khác) =====
    // BEFORE
    $CI->db->select('edi.product_id, edi.id_import, ed.warehouseman_date as dt, edi.type, edi.warehouses_id,CONCAT("export_different") as type_check,edi.quantity_payment as quantity_payment');
    $CI->db->from('tbltblexport_different_items edi');
    $CI->db->join('tblexport_different ed', 'ed.id = edi.id_export_different AND ed.warehouseman_id != 0', 'inner');
    $filterItem('edi.product_id', 'edi.type');             // type = product/nvl
    $filterWh('edi.warehouses_id');
    $CI->db->where('ed.warehouseman_date <', $beginMonth);
    $rows = $CI->db->get()->result_array();
    foreach ($rows as $r)
        $result['before'][$type_itemss][$r['product_id']][] = [
            'id_import' => $r['id_import'],
            'type_check' => $r['type_check'],
            'quantity_payment' => $r['quantity_payment'],
        ];

    // DURING
    $CI->db->select('edi.product_id, edi.id_import, ed.warehouseman_date as dt, edi.type, edi.warehouses_id,CONCAT("export_different") as type_check,edi.quantity_payment as quantity_payment');
    $CI->db->from('tbltblexport_different_items edi');
    $CI->db->join('tblexport_different ed', 'ed.id = edi.id_export_different AND ed.warehouseman_id != 0', 'inner');
    $filterItem('edi.product_id', 'edi.type');
    $filterWh('edi.warehouses_id');
    $CI->db->where('ed.warehouseman_date >=', $beginMonth);
    $CI->db->where('ed.warehouseman_date <=', $endMonth);
    $rows = $CI->db->get()->result_array();
    foreach ($rows as $r)
        $result['during'][$type_itemss][$r['product_id']][] = [
            'id_import' => $r['id_import'],
            'type_check' => $r['type_check'],
            'quantity_payment' => $r['quantity_payment'],
        ];

    // ===== Adjusted (giảm) type=2 =====
    // BEFORE
    $CI->db->select('ai.product_id, ai.id_import, a.date_create as dt, ai.type,CONCAT("adjusted") as type_check,ai.quantity_payment as quantity_payment');
    $CI->db->from('tbladjusted_items ai');
    $CI->db->join('tbladjusted a', 'a.id = ai.id_adjusted', 'inner');
    $CI->db->where('a.type', 2);
    $filterItem('ai.product_id', 'ai.type');
    $CI->db->where('a.date_create <', $beginMonth);
    $rows = $CI->db->get()->result_array();
    foreach ($rows as $r)
        $result['before'][$type_itemss][$r['product_id']][] = [
            'id_import' => $r['id_import'],
            'type_check' => $r['type_check'],
            'quantity_payment' => $r['quantity_payment'],
        ];

    // DURING
    $CI->db->select('ai.product_id, ai.id_import, a.date_create as dt, ai.type,CONCAT("adjusted") as type_check,ai.quantity_payment as quantity_payment');
    $CI->db->from('tbladjusted_items ai');
    $CI->db->join('tbladjusted a', 'a.id = ai.id_adjusted', 'inner');
    $CI->db->where('a.type', 2);
    $filterItem('ai.product_id', 'ai.type');
    $CI->db->where('a.date_create >=', $beginMonth);
    $CI->db->where('a.date_create <=', $endMonth);
    $rows = $CI->db->get()->result_array();
    foreach ($rows as $r)
        $result['during'][$type_itemss][$r['product_id']][] = [
            'id_import' => $r['id_import'],
            'type_check' => $r['type_check'],
            'quantity_payment' => $r['quantity_payment'],
        ];

    // ===== Transfer out (chuyển kho đi) =====
    // BEFORE
    $CI->db->select('twd.id_items as product_id, twd.id_import, tw.warehouseman_date as dt, twd.type, twd.warehouses_id,CONCAT("transfer") as type_check,twd.quantity_payment as quantity_payment');
    $CI->db->from('tbltransfer_warehouse_detail twd');
    $CI->db->join('tbltransfer_warehouse tw', 'tw.id = twd.id_transfer AND tw.warehouseman_id != 0', 'inner');
    $filterItem('twd.id_items', 'twd.type');
    $filterWh('twd.warehouses_id');
    $CI->db->where('tw.warehouseman_date <', $beginMonth);
    $rows = $CI->db->get()->result_array();
    foreach ($rows as $r)
        $result['before'][$type_itemss][$r['product_id']][] = [
            'id_import' => $r['id_import'],
            'type_check' => $r['type_check'],
            'quantity_payment' => $r['quantity_payment'],
        ];

    // DURING
    $CI->db->select('twd.id_items as product_id, twd.id_import, tw.warehouseman_date as dt, twd.type, twd.warehouses_id,CONCAT("transfer") as type_check,twd.quantity_payment as quantity_payment');
    $CI->db->from('tbltransfer_warehouse_detail twd');
    $CI->db->join('tbltransfer_warehouse tw', 'tw.id = twd.id_transfer AND tw.warehouseman_id != 0', 'inner');
    $filterItem('twd.id_items', 'twd.type');
    $filterWh('twd.warehouses_id');
    $CI->db->where('tw.warehouseman_date >=', $beginMonth);
    $CI->db->where('tw.warehouseman_date <=', $endMonth);
    $rows = $CI->db->get()->result_array();
    foreach ($rows as $r)
        $result['during'][$type_itemss][$r['product_id']][] = [
            'id_import' => $r['id_import'],
            'type_check' => $r['type_check'],
            'quantity_payment' => $r['quantity_payment'],
        ];

    // ===== Suggest exporting (xuất SX) =====
    // BEFORE
    $CI->db->select('sei.item_id as product_id, sei.id_import, se.date_warehouseman as dt, sei.type_item, sei.warehouse_item_id,CONCAT("suggest_exporting") as type_check,sei.quantity_payment as quantity_payment');
    $CI->db->from('tbl_suggest_exporting_items sei');
    $CI->db->join('tbl_suggest_exporting se', 'se.id = sei.suggest_exporting_id AND se.warehouseman_id != 0', 'inner');
    $filterItem('sei.item_id', 'sei.type_item');          // type_item = products/materials
    $filterWh('sei.warehouse_item_id');
    $CI->db->where('se.date_warehouseman <', $beginMonth);
    $rows = $CI->db->get()->result_array();
    foreach ($rows as $r)
        $result['before'][$type_itemss][$r['product_id']][] = [
            'id_import' => $r['id_import'],
            'type_check' => $r['type_check'],
            'quantity_payment' => $r['quantity_payment'],
        ];

    // DURING
    $CI->db->select('sei.item_id as product_id, sei.id_import, se.date_warehouseman as dt, sei.type_item, sei.warehouse_item_id,CONCAT("suggest_exporting") as type_check,sei.quantity_payment as quantity_payment');
    $CI->db->from('tbl_suggest_exporting_items sei');
    $CI->db->join('tbl_suggest_exporting se', 'se.id = sei.suggest_exporting_id AND se.warehouseman_id != 0', 'inner');
    $filterItem('sei.item_id', 'sei.type_item');
    $filterWh('sei.warehouse_item_id');
    $CI->db->where('se.date_warehouseman >=', $beginMonth);
    $CI->db->where('se.date_warehouseman <=', $endMonth);
    $rows = $CI->db->get()->result_array();
    foreach ($rows as $r)
        $result['during'][$type_itemss][$r['product_id']][] = [
            'id_import' => $r['id_import'],
            'type_check' => $r['type_check'],
            'quantity_payment' => $r['quantity_payment'],
        ];

    return $result;
}

function fetchImportValuesBatch(array $itemsToCost, $type_itemss, $beginMonth, $endMonth, array $warehouseIds)
{
    $CI = &get_instance();

    // 'product'|'nvl' -> cột type_item: 'products'|'materials'
    $type_sql = ($type_itemss === 'product') ? 'products' : 'materials';

    $map = [
        'before' => array_fill_keys($itemsToCost[$type_itemss] ?? [], 0.0),
        'during' => array_fill_keys($itemsToCost[$type_itemss] ?? [], 0.0),
    ];

    // Helpers
    $whereItems = function ($col) use ($CI, $itemsToCost, $type_itemss) {
        if (!empty($itemsToCost[$type_itemss])) {
            $CI->db->where_in($col, $itemsToCost[$type_itemss]);
        } else {
            // Không có item nào => trả về luôn map rỗng để khỏi query tốn công
            // (nhưng vì gọi nhiều lần, ta cứ để DB where_in rỗng thì CI sẽ bỏ qua)
        }
    };
    $whereWh = function ($col) use ($CI, $warehouseIds) {
        if (!empty($warehouseIds))
            $CI->db->where_in($col, $warehouseIds);
    };

    // =========================================================
    // 1) NHẬP MUA: tblimport_items (ii) + tblimport (i)
    //    Giá trị = SUM(ii.quantity_payment * ii.price)
    // ---------------------------------------------------------
    // BEFORE
    $CI->db->select('ii.product_id, SUM(ii.quantity_payment * ii.price) AS v', false);
    $CI->db->from('tblimport_items ii');
    $CI->db->join('tblimport i', 'i.id = ii.id_import AND i.warehouseman_id != 0', 'inner');
    $whereItems('ii.product_id');
    $whereWh('i.warehouse_id');
    $CI->db->where('ii.type', $type_itemss); // product|nvl
    $CI->db->where('i.warehouseman_date <', $beginMonth);
    $CI->db->group_by('ii.product_id');
    foreach ($CI->db->get()->result_array() as $r) {
        $pid = (int) $r['product_id'];
        if (array_key_exists($pid, $map['before']))
            $map['before'][$pid] += (float) $r['v'];
    }

    // DURING
    $CI->db->select('ii.product_id, SUM(ii.quantity_payment * ii.price) AS v', false);
    $CI->db->from('tblimport_items ii');
    $CI->db->join('tblimport i', 'i.id = ii.id_import AND i.warehouseman_id != 0', 'inner');
    $whereItems('ii.product_id');
    $whereWh('i.warehouse_id');
    $CI->db->where('ii.type', $type_itemss);
    $CI->db->where('i.warehouseman_date >=', $beginMonth);
    $CI->db->where('i.warehouseman_date <=', $endMonth);
    $CI->db->group_by('ii.product_id');
    foreach ($CI->db->get()->result_array() as $r) {
        $pid = (int) $r['product_id'];
        if (array_key_exists($pid, $map['during']))
            $map['during'][$pid] += (float) $r['v'];
    }

    // =========================================================
    // 2) ĐIỀU CHỈNH TĂNG: tbladjusted_items (ai) + tbladjusted (a, type = 1)
    //    Theo hàm PHP: product dùng quantity_net, nvl dùng quantity_payment
    // ---------------------------------------------------------
    $qtyCol = ($type_itemss === 'product') ? 'quantity_net' : 'quantity_payment';

    // BEFORE
    $CI->db->select("ai.product_id, SUM(ai.quantity_payment * ai.price) AS v", false);
    $CI->db->from('tbladjusted_items ai');
    $CI->db->join('tbladjusted a', 'a.id = ai.id_adjusted', 'inner');
    $whereItems('ai.product_id');
    $whereWh('a.warehouse_id');
    $CI->db->where('ai.type', $type_itemss); // product|nvl
    $CI->db->where('a.type', 1);
    $CI->db->where('a.date_create <', $beginMonth);
    $CI->db->group_by('ai.product_id');
    foreach ($CI->db->get()->result_array() as $r) {
        $pid = (int) $r['product_id'];
        if (array_key_exists($pid, $map['before']))
            $map['before'][$pid] += (float) $r['v'];
    }

    // DURING
    $CI->db->select("ai.product_id, SUM(ai.quantity_payment * ai.price) AS v", false);
    $CI->db->from('tbladjusted_items ai');
    $CI->db->join('tbladjusted a', 'a.id = ai.id_adjusted', 'inner');
    $whereItems('ai.product_id');
    $whereWh('a.warehouse_id');
    $CI->db->where('ai.type', $type_itemss);
    $CI->db->where('a.type', 1);
    $CI->db->where('a.date_create >=', $beginMonth);
    $CI->db->where('a.date_create <=', $endMonth);
    $CI->db->group_by('ai.product_id');
    foreach ($CI->db->get()->result_array() as $r) {
        $pid = (int) $r['product_id'];
        if (array_key_exists($pid, $map['during']))
            $map['during'][$pid] += (float) $r['v'];
    }

    // =========================================================
    // 3) CHUYỂN KHO NHẬN: tbltransfer_warehouse_detail (twd) + tbltransfer_warehouse (tw)
    //    Giá trị = SUM(twd.quantity_net * twd.price) (nhập theo lô chuyển đến)
    //    Lọc kho theo 'warehouses_to'
    // ---------------------------------------------------------
    // BEFORE
    $CI->db->select('twd.id_items AS product_id,twd.id_items,twd.type,twd.localtion_to,tw.id as idd', false);
    $CI->db->from('tbltransfer_warehouse_detail twd');
    $CI->db->join('tbltransfer_warehouse tw', 'tw.id = twd.id_transfer AND tw.warehouseman_id != 0', 'inner');
    $whereItems('twd.id_items');
    $whereWh('twd.warehouses_to');
    $CI->db->where('twd.type', $type_itemss); // product|nvl
    $CI->db->where('tw.warehouseman_date <', $beginMonth);
    $CI->db->group_by('twd.id_items');
    foreach ($CI->db->get()->result_array() as $r) {
        $pid = (int) $r['product_id'];
        $quantity_nets = get_table_where('tblwarehouse_product', array('import_id' => $r['idd'], 'type_export' => 2, 'product_id' => $r['id_items'], 'product_id' => $r['id_items'], 'type_items' => $r['type'], 'localtion' => $r['localtion_to']));
        $exists_quantity_import = 0;
        foreach ($quantity_nets as $ka => $vs) {
            // if ($vs['price'] == 0) {
            //     $vs['price'] = getpriceimportwarehouse($product_id, $type, 2);
            // }
            $exists_quantity_import += $vs['product_quantity_payment'] * $vs['price'];
        }
        if (array_key_exists($pid, $map['before']))
            $map['before'][$pid] += (float) $exists_quantity_import;
    }

    // DURING
    // $CI->db->select('twd.id_items AS product_id, SUM(twd.quantity_net * twd.price) AS v', false);
    $CI->db->select('twd.id_items AS product_id,twd.id_items,twd.type,twd.localtion_to,tw.id as idd', false);
    $CI->db->from('tbltransfer_warehouse_detail twd');
    $CI->db->join('tbltransfer_warehouse tw', 'tw.id = twd.id_transfer AND tw.warehouseman_id != 0', 'inner');
    $whereItems('twd.id_items');
    $whereWh('twd.warehouses_to');
    $CI->db->where('twd.type', $type_itemss);
    $CI->db->where('tw.warehouseman_date >=', $beginMonth);
    $CI->db->where('tw.warehouseman_date <=', $endMonth);
    $CI->db->group_by('twd.id_items');
    foreach ($CI->db->get()->result_array() as $r) {
        $pid = (int) $r['product_id'];
        $quantity_nets = get_table_where('tblwarehouse_product', array('import_id' => $r['idd'], 'type_export' => 2, 'product_id' => $r['id_items'], 'product_id' => $r['id_items'], 'type_items' => $r['type'], 'localtion' => $r['localtion_to']));
        $exists_quantity_import = 0;
        foreach ($quantity_nets as $ka => $vs) {
            // if ($vs['price'] == 0) {
            //     $vs['price'] = getpriceimportwarehouse($product_id, $type, 2);
            // }
            $exists_quantity_import += $vs['product_quantity_payment'] * $vs['price'];
        }
        if (array_key_exists($pid, $map['during']))
            $map['during'][$pid] += (float) $exists_quantity_import;
    }

    // =========================================================
    // 4) NHẬP THÀNH PHẨM: tbl_purchase_product_items (ppi) + tbl_purchase_products (pp)
    //    Giá trị = SUM(ppi.quantity * ppi.price)
    //    Lọc type_item = products|materials theo $type_sql
    // ---------------------------------------------------------
    // BEFORE
    $CI->db->select('ppi.item_id AS product_id, SUM(ppi.quantity * ppi.price) AS v', false);
    $CI->db->from('tbl_purchase_product_items ppi');
    $CI->db->join('tbl_purchase_products pp', 'pp.id = ppi.purchase_product_id AND pp.warehouseman_id != 0', 'inner');
    $whereItems('ppi.item_id');
    $whereWh('pp.warehouse_id');
    $CI->db->where('ppi.type_item', $type_sql);  // products|materials
    $CI->db->where('pp.date_warehouseman <', $beginMonth);
    $CI->db->group_by('ppi.item_id');
    foreach ($CI->db->get()->result_array() as $r) {
        $pid = (int) $r['product_id'];
        if (array_key_exists($pid, $map['before']))
            $map['before'][$pid] += (float) $r['v'];
    }

    // DURING
    $CI->db->select('ppi.item_id AS product_id, SUM(ppi.quantity * ppi.price) AS v', false);
    $CI->db->from('tbl_purchase_product_items ppi');
    $CI->db->join('tbl_purchase_products pp', 'pp.id = ppi.purchase_product_id AND pp.warehouseman_id != 0', 'inner');
    $whereItems('ppi.item_id');
    $whereWh('pp.warehouse_id');
    $CI->db->where('ppi.type_item', $type_sql);
    $CI->db->where('pp.date_warehouseman >=', $beginMonth);
    $CI->db->where('pp.date_warehouseman <=', $endMonth);
    $CI->db->group_by('ppi.item_id');
    foreach ($CI->db->get()->result_array() as $r) {
        $pid = (int) $r['product_id'];
        if (array_key_exists($pid, $map['during']))
            $map['during'][$pid] += (float) $r['v'];
    }

    // =========================================================
    // 5) NHẬP PHẾ LIỆU (INTERNAL): tbl_purchase_internal_items (pii) + tbl_purchase_internal (pi)
    //    Giá trị = SUM(pii.quantity * pii.price)
    //    Lọc type_item = products|materials theo $type_sql
    // ---------------------------------------------------------
    // BEFORE
    $CI->db->select('pii.item_id AS product_id, SUM(pii.quantity_payment * pii.price) AS v', false);
    $CI->db->from('tbl_purchase_internal_items pii');
    $CI->db->join('tbl_purchase_internal pi', 'pi.id = pii.purchase_internal_id AND pi.warehouseman_id != 0', 'inner');
    $whereItems('pii.item_id');
    $whereWh('pi.warehouse_id');
    $CI->db->where('pii.type_item', $type_sql);
    $CI->db->where('pi.date_warehouseman <', $beginMonth);
    $CI->db->group_by('pii.item_id');
    foreach ($CI->db->get()->result_array() as $r) {
        $pid = (int) $r['product_id'];
        if (array_key_exists($pid, $map['before']))
            $map['before'][$pid] += (float) $r['v'];
    }

    // DURING
    $CI->db->select('pii.item_id AS product_id, SUM(pii.quantity_payment * pii.price) AS v', false);
    $CI->db->from('tbl_purchase_internal_items pii');
    $CI->db->join('tbl_purchase_internal pi', 'pi.id = pii.purchase_internal_id AND pi.warehouseman_id != 0', 'inner');
    $whereItems('pii.item_id');
    $whereWh('pi.warehouse_id');
    $CI->db->where('pii.type_item', $type_sql);
    $CI->db->where('pi.date_warehouseman >=', $beginMonth);
    $CI->db->where('pi.date_warehouseman <=', $endMonth);
    $CI->db->group_by('pii.item_id');
    foreach ($CI->db->get()->result_array() as $r) {
        $pid = (int) $r['product_id'];
        if (array_key_exists($pid, $map['during']))
            $map['during'][$pid] += (float) $r['v'];
    }

    // =========================================================
    // 6) KHÁCH TRẢ HÀNG: tbl_returned_goods_items (rgi) + tbl_returned_goods (rg)
    //    Giá trị = SUM(rgi.quantity * rgi.price)
    //    Lọc type_item = products|materials theo $type_sql
    //    Lưu ý: lọc kho theo rgi.warehouse_id (đúng theo wh bạn đã dùng)
    // ---------------------------------------------------------
    // BEFORE
    $CI->db->select('rgi.item_id AS product_id, SUM(rgi.quantity * rgi.price) AS v', false);
    $CI->db->from('tbl_returned_goods_items rgi');
    $CI->db->join('tbl_returned_goods rg', 'rg.id = rgi.returned_goods_id AND rg.warehouseman_id != 0', 'inner');
    $whereItems('rgi.item_id');
    $whereWh('rgi.warehouse_id'); // theo chuẩn whReturnedGoods bạn đã viết
    $CI->db->where('rgi.type_item', $type_sql);
    $CI->db->where('rg.warehouseman_date <', $beginMonth);
    $CI->db->group_by('rgi.item_id');
    foreach ($CI->db->get()->result_array() as $r) {
        $pid = (int) $r['product_id'];
        if (array_key_exists($pid, $map['before']))
            $map['before'][$pid] += (float) $r['v'];
    }

    // DURING
    $CI->db->select('rgi.item_id AS product_id, SUM(rgi.quantity * rgi.price) AS v', false);
    $CI->db->from('tbl_returned_goods_items rgi');
    $CI->db->join('tbl_returned_goods rg', 'rg.id = rgi.returned_goods_id AND rg.warehouseman_id != 0', 'inner');
    $whereItems('rgi.item_id');
    $whereWh('rgi.warehouse_id');
    $CI->db->where('rgi.type_item', $type_sql);
    $CI->db->where('rg.warehouseman_date >=', $beginMonth);
    $CI->db->where('rg.warehouseman_date <=', $endMonth);
    $CI->db->group_by('rgi.item_id');
    foreach ($CI->db->get()->result_array() as $r) {
        $pid = (int) $r['product_id'];
        if (array_key_exists($pid, $map['during']))
            $map['during'][$pid] += (float) $r['v'];
    }

    return $map; // ['before' => [product_id => value], 'during' => [product_id => value]]
}
/*************  ✨ Windsurf Command ⭐  *************/
/**
 * Computes the export values from the given export rows for a specific product,
 * type items, and fallback price function.
 *
 * @param array $exportRows The export rows to compute from.
 * @param int $productId The ID of the product.
 * @param string $typeItems The type of items.
 * @param callable|null $fallbackPrice The fallback price function.
 * @return float The computed export values.
 */
/*******  61716fa0-d832-4f87-8d37-5e1b70b4137f  *******/

function computeExportValuesFromIdImport(array $exportRows, int $productId, string $typeItemss, callable $fallbackPrice = null)
{
    static $priceMemo = []; // memo cho wp_id => price
    $sum = 0.0;

    if ($fallbackPrice === null) {
        $fallbackPrice = function ($pid, $t) {
            // Bạn có thể đổi sang getpriceimportwarehouse(...) nếu muốn
            $p = (float) getpriceimport($pid, $t);
            return $p > 0 ? $p : 0.0;
        };
    }

    // 1) Parse tất cả id_import để gom wpIds
    $allPairs = []; // mỗi phần tử: ['wp_id' => int, 'qty' => float]
    $collectWpIds = [];
    foreach ($exportRows as $row) {

        $pairs = parseIdImportString($row['id_import'] ?? '', $row['type_check'] ?? '', $row['quantity_payment'] ?? 0);
        if (!empty($pairs)) {
            foreach ($pairs as $p) {
                // chỉ nhận cặp hợp lệ
                if (!empty($p['wp_id']) && isset($p['qty']) && $p['qty'] != 0) {
                    $allPairs[] = $p;
                    $collectWpIds[] = (int) $p['wp_id'];
                }
            }
        }
    }

    if (empty($allPairs)) {
        // Không có id_import hợp lệ => không tính được theo lô
        // Bạn có thể tùy chọn Fallback SL*giá TB, nhưng vì SL đã tính ở SQL, ở đây chỉ tính VALUE theo lô.
        return 0.0;
    }

    // 2) Tải giá cho các wp_id chưa có trong memo
    $needWpIds = array_values(array_unique(array_filter($collectWpIds)));
    $toFetch = [];
    foreach ($needWpIds as $id) {
        if (!array_key_exists($id, $priceMemo))
            $toFetch[] = $id;
    }
    if (!empty($toFetch)) {
        $fetched = loadWpPrices($toFetch); // [wp_id => price]
        // cache vào memo
        foreach ($toFetch as $id) {
            $priceMemo[$id] = isset($fetched[$id]) ? (float) $fetched[$id] : 0.0;
        }
    }

    // 3) Cộng tiền theo giá wp; nếu giá = 0, dùng fallback
    foreach ($allPairs as $p) {
        $wpId = (int) $p['wp_id'];
        $qty = (float) $p['qty'];

        $price = isset($priceMemo[$wpId]) ? (float) $priceMemo[$wpId] : 0.0;
        if ($price <= 0) {
            $price = (float) $fallbackPrice($productId, $typeItemss);
        }
        if ($price < 0)
            $price = 0.0; // vệ sinh

        $sum += $qty * $price;
    }

    return $sum;
}
function computeCostValuesBatch(array $itemsToCost, $type_itemss, $beginMonth, $endMonth, array $warehouseIds)
{
    $result = [];
    foreach ($itemsToCost as $t => $ids) {
        foreach ($ids as $id) {
            $result[$t][$id] = ['gt_dk' => 0.0, 'gt_nhap' => 0.0, 'gt_xuat' => 0.0, 'gt_ck' => 0.0];
        }
    }
    if (empty($itemsToCost[$type_itemss]))
        return $result;

    // 1) Nhập: lấy value before & during
    $importMap = fetchImportValuesBatch($itemsToCost, $type_itemss, $beginMonth, $endMonth, $warehouseIds);

    // 2) Xuất: gom row before & during -> parse id_import -> sum theo giá lô
    $exportRows = fetchExportRowsBatch($itemsToCost, $type_itemss, $beginMonth, $endMonth, $warehouseIds);

    // 3) Tính cho từng item
    foreach ($itemsToCost[$type_itemss] as $pid) {
        $gt_import_before = (float) ($importMap['before'][$pid] ?? 0);
        $gt_import_during = (float) ($importMap['during'][$pid] ?? 0);

        $rows_before = $exportRows['before'][$type_itemss][$pid] ?? [];
        $rows_during = $exportRows['during'][$type_itemss][$pid] ?? [];

        $rows_before = $exportRows['before'][$type_itemss][$pid] ?? [];
        $rows_during = $exportRows['during'][$type_itemss][$pid] ?? [];

        $gt_export_before = computeExportValuesFromIdImport($rows_before, $pid, $type_itemss);
        $gt_export_during = computeExportValuesFromIdImport($rows_during, $pid, $type_itemss);

        $gt_dk = $gt_import_before - $gt_export_before;
        $gt_nhap = $gt_import_during;
        $gt_xuat = $gt_export_during;
        $gt_ck = $gt_dk + $gt_nhap - $gt_xuat;

        $result[$type_itemss][$pid] = compact('gt_dk', 'gt_nhap', 'gt_xuat', 'gt_ck');
    }

    return $result;
}
function ConnectSocket($user_id, $user_name)
{
    $curl = curl_init();

    $db_name = APP_DB_NAME;
    $dataField = json_encode([
        'user_id' => $user_id,
        'user_name' => $user_name,
        'db_name' => $db_name
    ]);
    $link_connect_socket = get_option('link_connect_socket');
    curl_setopt_array($curl, array(
        CURLOPT_URL => $link_connect_socket . '/add-user',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 1,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $dataField,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($response, true);
    return !empty($response['token']) ? $response['token'] : NULL;
}

function sendSocket($data, $channels, $events)
{
    $curl = curl_init();
    $link_connect_socket = get_option('link_connect_socket');

    $db_name = APP_DB_NAME;
    $dataField = json_encode([
        'channels' => $channels,
        'event' => $events,
        'data' => $data,
        'db_name' => $db_name,
    ]);
    $data = $dataField;
    curl_setopt_array($curl, array(
        CURLOPT_URL => $link_connect_socket . '/send-notification',
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 1,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $dataField,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return true;
}
function taskCheckCreateBCKPH($id = '', $process_id = '', $detail_id = '')
{
    $CI = &get_instance();
    $CI->db->select('tbl_tasks_process_child.*');
    $CI->db->where('tbl_tasks_process_child.id_category_tasks', $process_id);
    $CI->db->where('tbl_tasks_process_child.task', $id);
    $category_hand_over = $CI->db->get('tbl_tasks_process_child')->result_array();
    $is_check = 1;
    foreach ($category_hand_over as $key => $value) {
        $check = get_table_where('tbl_tasks_inspection_criteria_process', ['tasks' => $id, 'process_id' => $process_id, 'id_tasks_process' => $detail_id, 'inspection_criteria' => $value['id']], '', 'row_array');

        $isCheckNot = '';
        if (!empty($check)) {
            if ($check['isCheckNot'] == 1) {
                $isCheckNot = 1;
            }
        }
        if ($isCheckNot == 1) {
            $production_report = get_table_where('tblproduction_report', ['id_tasks' => $id, 'id_tasks_process' => $detail_id, 'id_tasks_process_child' => $value['id']], '', 'row_array');

            if (!empty($production_report)) {
                $CI->db->select('tbl_process_production_report.*');
                $CI->db->where('tbl_process_production_report.staff_process', 0);
                $CI->db->where('tbl_process_production_report.production_report_id', $production_report['id']);
                $CI->db->from('tbl_process_production_report');
                $Success_process = $CI->db->get()->num_rows();
                if (!empty($Success_process)) {
                    $is_check = 2;
                }
            } else {
                $is_check = $value['id'];
            }
        }
    }
    return $is_check;
}
function TypePArray()
{
    $arrtype_p = [
        [
            'id' => 1,
            'name' => 'Giá trị cốt lỗi',
        ],
        [
            'id' => 2,
            'name' => 'Mức độ tuân thủ',
        ],
        [
            'id' => 3,
            'name' => 'Hiệu xuất',
        ],
    ];
    return $arrtype_p;
}

function createEvaluationEmployee($id = '', $staffid = '')
{
    if (!empty($id) && !empty($staffid)) {
        $CI = &get_instance();
        $CI->db->select('tblstaff.*, tblroles.day_evaluate');
        $CI->db->from('tblstaff');
        $CI->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
        $CI->db->join('tbl_role_level', 'tbl_role_level.id = tblstaff.role_level_id');
        $CI->db->where('tblstaff.staffid', $staffid);

        $dtStaff = $CI->db->get()->result_array();

        $count = 0;
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $code = getReference('probationary_assessment_ct');
                $option = [
                    'date' => date('Y-m-d H:i:s'),
                    'code' => $code,
                    'staff_id' => $staffid,
                    'role_id' =>  $value['role'] ?? 0,
                    'note' => null,
                    'level_target' => 0,
                    'level_achieved' => 0,
                    'date_start' => date('Y-m-d'),
                    'date_end' => date('Y-m-d'),
                    'point_b' => 0,
                    'point_c' => 0,
                    'point_d' => 0,
                    'point' => 0,
                    'type' => 2,
                    'rating_list' => 0,
                    'big_risk' => $id,
                    'rating' => $dtRating['name'] ?? null,
                    'point_start' => $dtRating['point_start'] ?? 0,
                    'point_end' => $dtRating['point_end'] ?? 0,
                    'check_fail_gate' => $dtRating['check_fail_gate'] ?? 0,
                    'date_created' => date('Y-m-d H:i:s'),
                    'created_by' => get_staff_user_id()
                ];


                $CI->db->insert('tbl_probationary_assessment', $option);
                $insert_id = $CI->db->insert_id();
                if ($insert_id) {
                    updateReference('probationary_assessment_ct');
                }
            }
        }
    }
}

function createEvaluationEmployeeold($id = '', $staffid = '')
{
    if (!empty($id) && !empty($staffid)) {
        $CI = &get_instance();
        $CI->db->select('tblstaff.*, tblroles.day_evaluate');
        $CI->db->from('tblstaff');
        $CI->db->join('tblroles', 'tblroles.roleid = tblstaff.role');
        $CI->db->join('tbl_role_level', 'tbl_role_level.id = tblstaff.role_level_id');
        $CI->db->where('tblstaff.staffid', $staffid);

        $dtStaff = $CI->db->get()->result_array();

        $count = 0;
        if (!empty($dtStaff)) {
            foreach ($dtStaff as $key => $value) {
                $arrItems = [];
                $CI->db->from('tbl_question_bank');
                $CI->db->where('role_id', $value['role'] ?? 0);
                $CI->db->where('role_level_id', $value['role_level_id'] ?? 0);
                $dtDataQuestion = $CI->db->get()->result_array();
                if (empty($dtDataQuestion)) {
                    continue;
                }
                foreach ($dtDataQuestion as $k => $v) {
                    $arrAnswer = [];
                    $CI->db->from('tbl_question_bank_answer');
                    $CI->db->where('tbl_question_bank_answer.question_bank_id', $v['id']);
                    $dtAnswer = $CI->db->get()->result_array();
                    if (!empty($dtAnswer)) {
                        foreach ($dtAnswer as $kk => $vv) {
                            $arrAnswer[] = [
                                'prefix' => $vv['prefix'],
                                'answer' => $vv['answer'],
                                'point' => $vv['point'],
                            ];
                        }
                    }
                    $arrItems[] = [
                        'question_bank_id' => $v['id'],
                        'weight' => $v['weight'],
                        'items' => $arrAnswer
                    ];
                }
                $code = 'DG-' . sprintf('%06d', ch_getMaxID('id', 'tbl_evaluation_employee') + 1);
                $option = [
                    'date' => date('Y-m-d H:i:s'),
                    'code' => $code,
                    'big_risk' => $id,
                    'type' => 1,
                    'staff_id' => $value['staffid'],
                    'role_id' => $value['role'],
                    'role_level_id' => $value['role_level_id'],
                    'note' => null,
                    'created_by' => get_staff_user_id(),
                    'date_created' => date('Y-m-d H:i:s')
                ];

                $CI->db->insert('tbl_evaluation_employee', $option);
                $insert_id = $CI->db->insert_id();
                if ($insert_id) {
                    foreach ($arrItems as $k => $v) {
                        $items = $v['items'];
                        unset($v['items']);
                        $v['evaluation_employee_id'] = $insert_id;
                        $CI->db->insert('tbl_evaluation_employee_question', $v);
                        $insert_id_item = $CI->db->insert_id();
                        if ($insert_id_item) {
                            if (!empty($items)) {
                                foreach ($items as $kk => $vv) {
                                    $vv['evaluation_employee_id'] = $insert_id;
                                    $vv['evaluation_employee_question_id'] = $insert_id_item;
                                    $CI->db->insert('tbl_evaluation_employee_question_answer', $vv);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
