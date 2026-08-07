<?php

defined('BASEPATH') or exit('No direct script access allowed');
$beginMonth =  '';
$endMonth   =  '';
$months_report = $this->ci->input->post('report_months');

if ($months_report != '') {
    $custom_date_select = '';
    if (is_numeric($months_report)) {
        // Last month
        if ($months_report == '1') {
            $beginMonth = date('Y-m-01', strtotime('first day of last month'));
            $endMonth   = date('Y-m-t', strtotime('last day of last month'));
        } else {
            $months_report = (int) $months_report;
            $months_report--;
            $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
            $endMonth   = date('Y-m-t');
        }
    } elseif ($months_report == 'this_month') {
        $beginMonth = date('Y-m-01');
        $endMonth   = date('Y-m-t');
    } elseif ($months_report == 'this_year') {
        $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
        $endMonth   = date('Y-m-d', strtotime(date('Y-12-31')));
    } elseif ($months_report == 'last_year') {
        $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
        $endMonth   = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
    } elseif ($months_report == 'custom') {
        $from_date = to_sql_date($this->ci->input->post('report_from'));
        $to_date   = to_sql_date($this->ci->input->post('report_to'));
        if ($from_date == $to_date) {
            $beginMonth =  $to_date;
            $endMonth   =  $to_date;
        } else {
            $beginMonth =  $from_date;
            $endMonth   =  $to_date;
        }
    }
    // $this->db->where($custom_date_select);
}
$type_import = $this->ci->input->post('type_import');


$warehouse_id = $this->ci->input->post('warehouse_id_array');
$type_purchase_products = $this->ci->input->post('type_purchase_products');
// if($warehouse_id == -1){
//     $warehouse_id = 0;
// }
$custom_item_select = $this->ci->input->post('custom_item_select');
$type_items = $this->ci->input->post('type_items');
if (!empty($type_items)) {
    if ($type_items == 'product') {
        $_type_items = 'products';
    } else
        if ($type_items == 'nvl') {
        $_type_items = 'materials';
    } else
        if ($type_items == 'tools') {
        $_type_items = 'tools_supplies';
    } else {
        $_type_items = 'items';
    }
}

$prices = '(SELECT t1.`price` as prices,t1.`product_id`,t1.`product_type`
                FROM tblgroup_price_detail t1
                INNER JOIN
                (
                    SELECT `product_id`,`product_type`, MAX(`price`) AS max_price
                    FROM tblgroup_price_detail
                    GROUP BY `product_id`,`product_type`
                ) t2
                    ON t1.`product_id` = t2.`product_id` AND t1.`product_type` = t2.`product_type` AND t1.price = t2.max_price GROUP BY `product_id`,`product_type`) prices';

$localtion_warehouses = ' (SELECT tbl_productions_orders_items.object_item_type,tbl_order_items.price as price_order,tbl_order_items.item_name,tbl_order_items.order_id,tbllocaltion_warehouses.id as localtion_warehousesid
FROM tbllocaltion_warehouses
INNER JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id
INNER JOIN tbl_productions_orders_items ON tbl_productions_orders_items.id = tbl_productions_orders_details.productions_orders_item_id AND tbl_productions_orders_items.object_item_type = "orders"
INNER JOIN tbl_order_items ON tbl_order_items.id = tbl_productions_orders_items.production_plan_item_id) localtion_warehouses';


//nhapkho thành phẩm
$select_import_tp = array(
    'tbl_purchase_products.date as date',
    'COALESCE(tbl_productions_orders.reference_no, po_temp.reference_no)',
    'tbl_purchase_products.reference_no as code',
    '1',
    '2',
    '3',
    '4',
    'tblwarehouse.name as name_warehouse',
    'tbl_purchase_product_items.location_id as localtion_id',
    'tbl_purchase_product_items.quantity_stock as quantity',
    'tbl_purchase_product_items.quantity_stock as quantity_net',
    'IF(object_item_type = "orders",price_order,prices) as prices',
    // 'price_order as total_price'
    '(IF(object_item_type = "orders",price_order,prices) * tbl_purchase_product_items.quantity_unit) as total_price',
    'tbl_purchase_products.note as note'

);
$where_import_tp = array(
    // 'AND tbl_purchase_product_items.type_item = "products"',
    'AND tbl_purchase_products.warehouseman_id != 0',
);

if (!empty($warehouse_id)) {
    array_push($where_import_tp, 'AND tbl_purchase_products.warehouse_id IN (' . implode(',', $warehouse_id) . ')');
}
// if (!empty($type_items) && $type_items == 'tools') {
//     array_push($where_import_tp, 'AND tbl_purchase_product_items.item_id =', $custom_item_select);
// } elseif (!empty($type_items) && ($type_items != 'tools')) {
//     array_push($where_import_tp, 'AND tbl_purchase_product_items.item_id =', 0);
// }
if (!empty($type_import)) {
    if ($type_import != 2) {
        array_push($where_import_tp, 'AND tbl_purchase_products.id = "-1"');
    }
}
if (!empty($type_items)) {
    array_push($where_import_tp, 'AND tbl_purchase_product_items.item_id =', $custom_item_select);
    array_push($where_import_tp, 'AND tbl_purchase_product_items.type_item = "' . $_type_items . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($where_import_tp, 'AND tbl_purchase_products.date >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($where_import_tp, 'AND tbl_purchase_products.date <=' . '"' . $endMonth . ' 23:59:59"');
}

$aColumns_import_tp     = $select_import_tp;
$sIndexColumn_import_tp = "id";
$sTable_import_tp       = 'tbl_purchase_product_items';
$join_import_tp         = array(
    'LEFT JOIN tbl_purchase_products ON tbl_purchase_products.id = tbl_purchase_product_items.purchase_product_id',
    'LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_purchase_products.productions_orders_details_id',
    'LEFT JOIN tbl_productions_orders ON tbl_productions_orders.id = tbl_productions_orders_details.productions_orders_id',
    'LEFT JOIN tbl_productions_orders po_temp ON po_temp.id = tbl_purchase_products.po_id',

    'LEFT JOIN tblwarehouse ON tblwarehouse.id = tbl_purchase_products.warehouse_id',
    'LEFT JOIN ' . $prices . ' ON prices.product_id = tbl_purchase_product_items.item_id AND prices.product_type = "product"',
    'LEFT JOIN ' . $localtion_warehouses . ' ON localtion_warehouses.localtion_warehousesid = tbl_purchase_product_items.location_id',
);

// array_push($join_import_tp, 'LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbl_purchase_product_items.productions_orders_details_id');

if (!empty($type_purchase_products)) {
    
    if ($type_purchase_products == 1) {
        array_push($where_import_tp, 'AND (( tbl_productions_orders_details.object_type = "business_plan" AND tbl_purchase_products.type_business_plan = 0 AND tbl_purchase_products.final_stage = 1) OR tbl_purchase_products.pois_id = 0)');
    }elseif ($type_purchase_products == 2) {
        array_push($where_import_tp, 'AND tbl_productions_orders_details.object_type = "orders"');
    }elseif ($type_purchase_products == 3) {
        array_push($where_import_tp, 'AND tbl_purchase_products.is_errors = 1');
    }elseif ($type_purchase_products == 4) {
        array_push($where_import_tp, 'AND tbl_productions_orders_details.object_type = "business_plan"');
        array_push($where_import_tp, 'AND tbl_purchase_products.type_business_plan = 1');
    }
}

$order_by_import_tp = 'order by item_id asc';
$result_import_tp   = data_tables_init($aColumns_import_tp, $sIndexColumn_import_tp, $sTable_import_tp, $join_import_tp, $where_import_tp, array('tbl_purchase_products.id as id_main,tbl_purchase_product_items.item_id as product_id,type_item as type,9 as exists_quantity'));

$output_import_tp  = $result_import_tp['output'];
$rResult_import_tp = $result_import_tp['rResult'];

$aColumnsG = array(
    'date',
    'COALESCE(tbl_productions_orders.reference_no, po_temp.reference_no)',
    'code',
    '1',
    '2',
    '3',
    '4',
    'name_warehouse',
    'localtion_id',
    'quantity',
    'quantity_net',
    'prices',
    'total_price',
    'note'
);
$rResultG = array();
// if (!empty($rResultimport)) {
//     $rResultG = array_merge($rResultG, $rResultimport);
// }
// if (!empty($rResult_internal)) {
//     $rResultG = array_merge($rResultG, $rResult_internal);
// }
// if (!empty($rResult_adjustedT)) {
//     $rResultG = array_merge($rResultG, $rResult_adjustedT);
// }
// if (!empty($rResult_TranfersN)) {
//     $rResultG = array_merge($rResultG, $rResult_TranfersN);
// }
if (!empty($rResult_import_tp)) {
    $rResultG = array_merge($rResultG, $rResult_import_tp);
}
$rResultG = $rResult_import_tp;

// if (!empty($rResult_import_gc)) {
//     $rResultG = array_merge($rResultG, $rResult_import_gc);
// }
if (!empty($rResultG)) {
    usort($rResultG, ch_make_cmp(['date' => "asc"]));
}
// $output = $outputimport;
// $output['iTotalRecords']=$outputimport['iTotalRecords']+ $output_TranfersN['iTotalRecords']  + $output_adjustedT['iTotalRecords'] + $output_import_tp['iTotalRecords'];
// $output['iTotalDisplayRecords']=$outputimport['iTotalDisplayRecords'] + $output_adjustedT['iTotalDisplayRecords'] + $output_TranfersN['iTotalDisplayRecords'] + $output_import_tp['iTotalDisplayRecords'];
$output['iTotalRecords'] = $output_import_tp['iTotalRecords'];
$output['iTotalDisplayRecords'] = $output_import_tp['iTotalDisplayRecords'];
$currentPage = $this->ci->input->post('start');
$sumFExistsQ = 0;
$footer_data = array(
    'product_quantity' => 0,
    'product_total' => 0,
);
$output['aaData'] = array();
foreach ($rResultG as $key => $aRow) {
    $row = [];
    if ($aRow['type'] == 'items') {
        $_type_items = 'items';
    } elseif ($aRow['type'] == 'nvl') {
        $_type_items = 'materials';
    } elseif ($aRow['type'] == 'tools') {
        $_type_items = 'tools_supplies';
    } else {
        $_type_items = 'products';
    }
    $get_items = get_items($aRow['product_id'], $aRow['type']);
 
    for ($i = 0; $i < count($aColumnsG); $i++) {
        if (strpos($aColumnsG[$i], 'as') !== false && !isset($aRow[$aColumnsG[$i]])) {
            $_data = $aRow[strafter($aColumnsG[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumnsG[$i]];
        }
        if ($aColumnsG[$i] == '1') {
            $_data = format_item_purchases($aRow['type']);
        }
        if ($aColumnsG[$i] == '2') {
            $_data = $get_items->code;
        }
        if ($aColumnsG[$i] == '3') {
            $_data = $get_items->name;
        }
        if ($aColumnsG[$i] == '4') {
            $_data = $get_items->unit_name_stock;
        }
        if ($aColumnsG[$i] == 'prices') {
            $_data = '<div class="text-right">' . formatNumber($aRow['prices']) . '</div>';
        }
        if ($aColumnsG[$i] == 'total_price') {
            $_data = '<div class="text-right">' . formatNumber($aRow['total_price']) . '</div>';
            $footer_data['product_total']+=$aRow['total_price'];
        }
        if ($aColumnsG[$i] == 'date') {
            $_data = '<div class="text-center">' . _dhau($aRow['date']) . '<div>';
        }
        if ($aColumnsG[$i] == 'warehouseman_date') {
            $_data = '<div class="text-center">' . _d($aRow['warehouseman_date']) . '<div>';
        }
        if ($aColumnsG[$i] == 'localtion_id') {
            $_data = '<div class="text-center">' . get_listname_localtion_warehouse($aRow['localtion_id']) . '<div>';
        }
        if ($aColumnsG[$i] == 'quantity') {
            $_data = '<div class="text-center">' . formatNumber($aRow['quantity']) . '<div>';
        }
        if ($aColumnsG[$i] == 'quantity_net') {
            $_data = '<div class="text-center">' . formatNumber($aRow['quantity_net']) . '<div>';
            $footer_data['product_quantity']+=$aRow['quantity_net'];
        }
        if ($aColumnsG[$i] == 'code') {
            if ($aRow['exists_quantity'] == 1) {
                $_data = '<a href="#" onclick="view_import(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>';
            } elseif ($aRow['exists_quantity'] == 5) {
                $_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>';
            } elseif ($aRow['exists_quantity'] == 6) {
                $_data = '<a href="#" onclick="view_transfer(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a> ';
            } elseif ($aRow['exists_quantity'] == 9) {
                $_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('stock/view_purchase_product/' . $aRow['id_main']) . '" data-toggle="modal" data-target="#myModal">' . $_data . '</a>';
            } elseif ($aRow['exists_quantity'] == 16) {
                $_data = '<a class="tnh-modal" title="' . _l('purchase_internal') . '" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . admin_url('stock/view_purchase_internal/' . $aRow['id_main']) . '">' . $_data . '</a>';
            } elseif ($aRow['exists_quantity'] == 687) {
                $_data = '<a class="tnh-modal" title="' . _l('Nhập gia công') . '" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . admin_url('outsource/view_import_outsource/' . $aRow['id_main']) . '">' . $_data . '</a>  ';
            }
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = formatNumber($total);
}
$output['sums'] = $footer_data;

$output['title_excel'] = ['Giai Đoạn : ' . (!empty($beginMonth) ? _dt($beginMonth) : '') . ' - ' . (!empty($endMonth) ? _dt($endMonth) : ''), 'KHO HÀNG: TẤT CẢ'];
$warehouse_id_array = $this->ci->input->post('warehouse_id_array');
if(!empty($warehouse_id_array)) {
    $this->ci->db->where_in('id', $warehouse_id_array);
    $listWarehouseSearch = $this->ci->db->get('tblwarehouse')->result_array();
    if(!empty($listWarehouseSearch)) {
        $output['title_excel'][1] = 'KHO HÀNG: ';
        foreach($listWarehouseSearch as $key => $value) {
            $output['title_excel'][1] .= $value['name'] .', ';
        }
        $output['title_excel'][1] = trim($output['title_excel'][1], ', ');
    }
}