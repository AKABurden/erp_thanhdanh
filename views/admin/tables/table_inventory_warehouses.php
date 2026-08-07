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


$warehouse_id = $this->ci->input->post('warehouse_id_array');
if ($warehouse_id == -1) {
    $warehouse_id = 0;
}
$custom_item_select = $id_items;
$type_items = $type;
//Nhập kho
$selectimport = array(
    'tblimport.warehouseman_date as warehouseman_date',
    'tblimport.date as date',
    'concat(tblimport.prefix,"-",tblimport.code) as code',
    'tblimport.warehouse_id as warehouse_id',
    'tblimport.note as reason',
    '1',
    'tblimport_items.quantity_stock as check_quantity'
);


$whereimport = array(
    'AND tblimport.warehouseman_id != 0'
);
if (!empty($warehouse_id)) {
    $whereimport = array(
        'AND tblimport.warehouse_id IN (' . implode(',', $warehouse_id) . ')',
        'AND tblimport.warehouseman_id != 0',
    );
}

if (!empty($type_items)) {
    array_push($whereimport, 'AND tblimport_items.product_id =', $custom_item_select);
    array_push($whereimport, 'AND tblimport_items.type = "' . $type_items . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($whereimport, 'AND tblimport.warehouseman_date >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($whereimport, 'AND tblimport.warehouseman_date <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 2) {
    array_push($whereimport, 'AND tblimport.id  = -1');
}
$aColumnsimport     = $selectimport;
$sIndexColumnimport = "id";
$sTableimport       = 'tblimport_items';
$joinimport         = array(
    'LEFT JOIN tblimport ON tblimport.id = tblimport_items.id_import',

);

$order_byimport = 'order by product_id asc';
$resultimport  = data_tables_init($aColumnsimport, $sIndexColumnimport, $sTableimport, $joinimport, $whereimport, array('tblimport.id as id_main,tblimport_items.product_id as product_id,tblimport_items.type as type,tblimport_items.localtion_warehouses_id as localtion_id,1 as exists_quantity'));

$outputimport  = $resultimport['output'];
$rResultimport = $resultimport['rResult'];
//Xuất kho
// if ($type_items == 'product') {
//     $type_itemss = 'products';
// } else
//             if ($type_items == 'nvl') {
//     $type_itemss = 'materials';
// } else
//             if ($type_items == 'tools') {
//     $type_itemss = 'tools_supplies';
// } else {
//     $type_itemss = $type_items;
// }

if ($type_items == 'items') {
    $type_itemss = 'items';
} elseif ($type_items == 'nvl') {
    $type_itemss = 'materials';
} elseif ($type_items == 'tools') {
    $type_itemss = 'tools_supplies';
} else {
    $type_itemss = 'products';
}

if ($type_items == 'items') {
    $_type_items = 'items';
} elseif ($type_items == 'nvl') {
    $_type_items = 'materials';
} elseif ($type_items == 'tools') {
    $_type_items = 'tools_supplies';
} else {
    $_type_items = 'products';
}

// if ($type_items == 'product') {
//     $_type_items = 'products';
// } else
//         if ($type_items == 'nvl') {
//     $_type_items = 'materials';
// } else
//         if ($type_items == 'tools') {
//     $_type_items = 'tools_supplies';
// } else {
//     $_type_items = 'items';
// }

//xuất kho
$selectexport = array(
    'tbl_deliveries.date_warehouseman as warehouseman_date',
    'tbl_deliveries.date as date',
    'tbl_deliveries.reference_no as code',
    'tbl_delivery_items.warehouse_id as warehouse_id',
    'tbl_deliveries.note as reason',
    '1',
    'tbl_delivery_items.quantity_stock as check_quantity',
);
// $whereexport = array(
//     'AND tbl_delivery_items.warehouse_id IN (' . implode(',', $warehouse_id) . ')',
//     'AND tbl_deliveries.warehouseman_id != 0',
// );
$whereexport = array(
    'AND tbl_deliveries.warehouseman_id != 0'
);
if (!empty($warehouse_id)) {
    $whereexport = array(
        'AND tbl_delivery_items.warehouse_id IN (' . implode(',', $warehouse_id) . ')',
        'AND tbl_deliveries.warehouseman_id != 0'
    );
}
if (!empty($type_items)) {
    array_push($whereexport, 'AND tbl_delivery_items.item_id =', $custom_item_select);
    array_push($whereexport, 'AND tbl_delivery_items.type_item = "' . $type_itemss . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($whereexport, 'AND tbl_deliveries.date_warehouseman >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($whereexport, 'AND tbl_deliveries.date_warehouseman <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 1) {
    array_push($whereexport, 'AND tbl_deliveries.id  = -1');
}
$aColumnsexport     = $selectexport;
$sIndexColumnexport = "id";
$sTableexport       = 'tbl_delivery_items';
$joinexport         = array(
    'LEFT JOIN tbl_deliveries ON tbl_deliveries.id = tbl_delivery_items.delivery_id',

);

$order_byexport = 'order by item_id asc';
$resultexport   = data_tables_init_nolimt($aColumnsexport, $sIndexColumnexport, $sTableexport, $joinexport, $whereexport, array('tbl_deliveries.id as id_main,tbl_delivery_items.item_id as product_id,' . ch_where('tbl_delivery_items', 'type_item') . ' as type,tbl_delivery_items.location_id as localtion_id,id_import,99 as exists_quantity'));

$outputexport  = $resultexport['output'];
$rResultexport = $resultexport['rResult'];

//trả hàng NCC
$select_return = array(
    'tblreturn_suppliers.data_warehouseman as warehouseman_date',
    'tblreturn_suppliers.date as date',
    'concat(tblreturn_suppliers.prefix,"",tblreturn_suppliers.code) as code',
    'tblreturn_suppliers.warehouse_id as warehouse_id',
    'tblreturn_suppliers.note as reason',
    '1',
    'tblreturn_suppliers_items.quantity_net as check_quantity'
);
// $where_return = array(
//     'AND tblreturn_suppliers.warehouse_id IN (' . implode(',', $warehouse_id) . ')',
//     'AND tblreturn_suppliers.warehouseman_id != 0',
// );
$where_return = array(
    'AND tblreturn_suppliers.warehouseman_id != 0'
);
if (!empty($warehouse_id)) {
    $where_return = array(
        'AND tblreturn_suppliers.warehouse_id IN (' . implode(',', $warehouse_id) . ')',
        'AND tblreturn_suppliers.warehouseman_id != 0',
    );
}

if (!empty($type_items)) {
    array_push($where_return, 'AND tblreturn_suppliers_items.product_id =', $custom_item_select);
    array_push($where_return, 'AND tblreturn_suppliers_items.type = "' . $type_items . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($where_return, 'AND tblreturn_suppliers.data_warehouseman >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($where_return, 'AND tblreturn_suppliers.data_warehouseman <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 1) {
    array_push($where_return, 'AND tblreturn_suppliers.id  = -1');
}
$aColumns_return     = $select_return;
$sIndexColumn_return = "id";
$sTable_return       = 'tblreturn_suppliers_items';
$join_return         = array(
    'LEFT JOIN tblreturn_suppliers ON tblreturn_suppliers.id = tblreturn_suppliers_items.id_return',
);

$order_by_return = 'order by product_id asc';
$result_return   = data_tables_init($aColumns_return, $sIndexColumn_return, $sTable_return, $join_return, $where_return, array('tblreturn_suppliers.id as id_main,tblreturn_suppliers_items.product_id as product_id,tblreturn_suppliers_items.type as type,tblreturn_suppliers_items.localtion_warehouses_id as localtion_id,id_import,3 as exists_quantity'));

$output_return  = $result_return['output'];
$rResult_return = $result_return['rResult'];

//Điều chỉnh kho giảm
$select_adjustedG = array(
    'tbladjusted.date_create as warehouseman_date',
    'tbladjusted.date as date',
    'concat(tbladjusted.prefix,"",tbladjusted.code) as code',
    'tbladjusted.warehouse_id as warehouse_id',
    'tbladjusted.note as reason',
    '1',
    'tbladjusted_items.quantity_net as check_quantity'
);
// $where_adjustedG = array(
//     'AND tbladjusted.warehouse_id IN (' . implode(',', $warehouse_id) . ')',
//     'AND tbladjusted.type = 2',
// );
$where_adjustedG = array(
    'AND tbladjusted.type = 2'
);
if (!empty($warehouse_id)) {
    $where_adjustedG = array(
        'AND tbladjusted.warehouse_id IN (' . implode(',', $warehouse_id) . ')',
        'AND tbladjusted.type = 2',
    );
}
if (!empty($type_items)) {
    array_push($where_adjustedG, 'AND tbladjusted_items.product_id =', $custom_item_select);
    array_push($where_adjustedG, 'AND tbladjusted_items.type = "' . $type_items . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($where_adjustedG, 'AND tbladjusted.date_create >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($where_adjustedG, 'AND tbladjusted.date_create <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 1) {
    array_push($where_adjustedG, 'AND tbladjusted.id  = -1');
}
$aColumns_adjustedG     = $select_adjustedG;
$sIndexColumn_adjustedG = "id";
$sTable_adjustedG       = 'tbladjusted_items';
$join_adjustedG         = array(
    'LEFT JOIN tbladjusted ON tbladjusted.id = tbladjusted_items.id_adjusted',
);

$order_by_adjustedG = 'order by product_id asc';
$result_adjustedG   = data_tables_init($aColumns_adjustedG, $sIndexColumn_adjustedG, $sTable_adjustedG, $join_adjustedG, $where_adjustedG, array('tbladjusted.id as id_main,tbladjusted_items.product_id as product_id,tbladjusted_items.type as type,tbladjusted_items.localtion as localtion_id,id_import,4 as exists_quantity'));

$output_adjustedG  = $result_adjustedG['output'];
$rResult_adjustedG = $result_adjustedG['rResult'];

//Điều chỉnh kho tăng
$select_adjustedT = array(
    'tbladjusted.date_create as warehouseman_date',
    'tbladjusted.date as date',
    'concat(tbladjusted.prefix,"",tbladjusted.code) as code',
    'tbladjusted.warehouse_id as warehouse_id',
    'tbladjusted.note as reason',
    '1',
    'tbladjusted_items.quantity_net as check_quantity'
);
// $where_adjustedT = array(
//     'AND tbladjusted.warehouse_id IN (' . implode(',', $warehouse_id) . ')',
//     'AND tbladjusted.type = 1',
// );
$where_adjustedT = array(
    'AND tbladjusted.type = 1'
);
if (!empty($warehouse_id)) {
    $where_adjustedT = array(
        'AND tbladjusted.warehouse_id IN (' . implode(',', $warehouse_id) . ')',
        'AND tbladjusted.type = 1',
    );
}
if (!empty($type_items)) {
    array_push($where_adjustedT, 'AND tbladjusted_items.product_id =', $custom_item_select);
    array_push($where_adjustedT, 'AND tbladjusted_items.type = "' . $type_items . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($where_adjustedT, 'AND tbladjusted.date_create >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($where_adjustedT, 'AND tbladjusted.date_create <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 2) {
    array_push($where_adjustedT, 'AND tbladjusted.id  = -1');
}
$aColumns_adjustedT     = $select_adjustedT;
$sIndexColumn_adjustedT = "id";
$sTable_adjustedT       = 'tbladjusted_items';
$join_adjustedT         = array(
    'LEFT JOIN tbladjusted ON tbladjusted.id = tbladjusted_items.id_adjusted',

);

$order_by_adjustedT = 'order by product_id asc';
$result_adjustedT   = data_tables_init($aColumns_adjustedT, $sIndexColumn_adjustedT, $sTable_adjustedT, $join_adjustedT, $where_adjustedT, array('tbladjusted.id as id_main,tbladjusted_items.product_id as product_id,tbladjusted_items.type as type,tbladjusted_items.localtion as localtion_id,5 as exists_quantity'));

$output_adjustedT  = $result_adjustedT['output'];
$rResult_adjustedT = $result_adjustedT['rResult'];

//Chuyển kho: nhận
$select_TranfersN = array(
    'tbltransfer_warehouse.warehouseman_date as warehouseman_date',
    'tbltransfer_warehouse.date as date',
    'concat(tbltransfer_warehouse.prefix,"-",tbltransfer_warehouse.code) as code',
    'tbltransfer_warehouse_detail.warehouses_to as warehouse_id',
    'tbltransfer_warehouse.note as reason',
    '1',
    'tbltransfer_warehouse_detail.quantity_net as check_quantity'
);
// $where_TranfersN = array(
//     'AND tbltransfer_warehouse_detail.warehouses_to IN (' . implode(',', $warehouse_id) . ')',
//     'AND tbltransfer_warehouse.warehouseman_id != 0',
// );
$where_TranfersN = array(
    'AND tbltransfer_warehouse.warehouseman_id != 0'
);
if (!empty($warehouse_id)) {
    $where_TranfersN = array(
        'AND tbltransfer_warehouse_detail.warehouses_to IN (' . implode(',', $warehouse_id) . ')',
        'AND tbltransfer_warehouse.warehouseman_id != 0',
    );
}
if (!empty($type_items)) {
    array_push($where_TranfersN, 'AND tbltransfer_warehouse_detail.id_items =', $custom_item_select);
    array_push($where_TranfersN, 'AND tbltransfer_warehouse_detail.type = "' . $type_items . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($where_TranfersN, 'AND tbltransfer_warehouse.warehouseman_date >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($where_TranfersN, 'AND tbltransfer_warehouse.warehouseman_date <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 2) {
    array_push($where_TranfersN, 'AND tbltransfer_warehouse.id  = -1');
}
$aColumns_TranfersN     = $select_TranfersN;
$sIndexColumn_TranfersN = "id";
$sTable_TranfersN       = 'tbltransfer_warehouse_detail';
$join_TranfersN         = array(
    'LEFT JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer',

);

$order_by_TranfersN = 'order by id_items asc';
$result_TranfersN   = data_tables_init($aColumns_TranfersN, $sIndexColumn_TranfersN, $sTable_TranfersN, $join_TranfersN, $where_TranfersN, array('tbltransfer_warehouse.id as id_main,tbltransfer_warehouse_detail.id_items as product_id,tbltransfer_warehouse_detail.type as type,tbltransfer_warehouse_detail.localtion_to as localtion_id,6 as exists_quantity'));

$output_TranfersN  = $result_TranfersN['output'];
$rResult_TranfersN = $result_TranfersN['rResult'];

//Chuyển kho: di
$select_TranfersD = array(
    'tbltransfer_warehouse.warehouseman_date as warehouseman_date',
    'tbltransfer_warehouse.date as date',
    'concat(tbltransfer_warehouse.prefix,"-",tbltransfer_warehouse.code) as code',
    'tbltransfer_warehouse_detail.warehouses_id as warehouse_id',
    'tbltransfer_warehouse.note as reason',
    '1',
    'tbltransfer_warehouse_detail.quantity_net as check_quantity'
);
// $where_TranfersD = array(
//     'AND tbltransfer_warehouse_detail.warehouses_id IN (' . implode(',', $warehouse_id) . ')',
//     'AND tbltransfer_warehouse.warehouseman_id != 0',
// );
$where_TranfersD = array(
    'AND tbltransfer_warehouse.warehouseman_id != 0'
);
if (!empty($warehouse_id)) {
    $where_TranfersD = array(
        'AND tbltransfer_warehouse_detail.warehouses_id IN (' . implode(',', $warehouse_id) . ')',
        'AND tbltransfer_warehouse.warehouseman_id != 0',
    );
}
if (!empty($type_items)) {
    array_push($where_TranfersD, 'AND tbltransfer_warehouse_detail.id_items =', $custom_item_select);
    array_push($where_TranfersD, 'AND tbltransfer_warehouse_detail.type = "' . $type_items . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($where_TranfersD, 'AND tbltransfer_warehouse.warehouseman_date >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($where_TranfersD, 'AND tbltransfer_warehouse.warehouseman_date <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 1) {
    array_push($where_TranfersD, 'AND tbltransfer_warehouse.id  = -1');
}
$aColumns_TranfersD     = $select_TranfersD;
$sIndexColumn_TranfersD = "id";
$sTable_TranfersD       = 'tbltransfer_warehouse_detail';
$join_TranfersD         = array(
    'LEFT JOIN tbltransfer_warehouse ON tbltransfer_warehouse.id = tbltransfer_warehouse_detail.id_transfer',

);

$order_by_TranfersD = 'order by id_items asc';
$result_TranfersD   = data_tables_init($aColumns_TranfersD, $sIndexColumn_TranfersD, $sTable_TranfersD, $join_TranfersD, $where_TranfersD, array('tbltransfer_warehouse.id as id_main,tbltransfer_warehouse_detail.id_items as product_id,tbltransfer_warehouse_detail.type as type,tbltransfer_warehouse_detail.localtion_id as localtion_id,id_import,66 as exists_quantity'));

$output_TranfersD  = $result_TranfersD['output'];
$rResult_TranfersD = $result_TranfersD['rResult'];


//xuat kho khac
$select_export_different = array(
    'tblexport_different.warehouseman_date as warehouseman_date',
    'tblexport_different.date as date',
    'concat(tblexport_different.prefix,"-",tblexport_different.code) as code',
    'tbltblexport_different_items.warehouses_id as warehouse_id',
    'tblexport_different.note as reason',
    '1',
    'tbltblexport_different_items.quantity_net as check_quantity'
);
// $where_export_different = array(
//     'AND tbltblexport_different_items.warehouses_id IN (' . implode(',', $warehouse_id) . ')',
// );
$where_export_different = array();
if (!empty($warehouse_id)) {
    $where_export_different = array(
        'AND tbltblexport_different_items.warehouses_id IN (' . implode(',', $warehouse_id) . ')',
    );
}
if (!empty($type_items)) {
    array_push($where_export_different, 'AND tbltblexport_different_items.product_id =', $custom_item_select);
    array_push($where_export_different, 'AND tbltblexport_different_items.type = "' . $type_items . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($where_export_different, 'AND tblexport_different.warehouseman_date >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($where_export_different, 'AND tblexport_different.warehouseman_date <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 1) {
    array_push($where_export_different, 'AND tblexport_different.id  = -1');
}
$aColumns_export_different     = $select_export_different;
$sIndexColumn_export_different = "id";
$sTable_export_different       = 'tbltblexport_different_items';
$join_export_different         = array(
    'LEFT JOIN tblexport_different ON tblexport_different.id = tbltblexport_different_items.id_export_different',

);

$order_by_export_different = 'order by product_id asc';
$result_export_different   = data_tables_init($aColumns_export_different, $sIndexColumn_export_different, $sTable_export_different, $join_export_different, $where_export_different, array('tblexport_different.id as id_main,tbltblexport_different_items.product_id as product_id,tbltblexport_different_items.type as type,tbltblexport_different_items.localtion_warehouses_id as localtion_id,888 as exists_quantity'));

$output_export_different  = $result_export_different['output'];
$rResult_export_different = $result_export_different['rResult'];

if ($type_items == 'product') {
    $type_items = 'products';
} else
        if ($type_items == 'nvl') {
    $type_items = 'materials';
} else
        if ($type_items == 'tools') {
    $type_items = 'tools_supplies';
}
//Xuất kho sản xuất
$select_exportsx = array(
    'tbl_suggest_exporting.date_warehouseman as warehouseman_date',
    'tbl_suggest_exporting.date as date',
    'tbl_suggest_exporting.reference_stock as code',
    'tbl_suggest_exporting_items.warehouse_item_id as warehouse_id',
    'tbl_suggest_exporting.note as reason',
    '1',
    'tbl_suggest_exporting_items.quantity_warehouse as check_quantity'
);
// $where_exportsx = array(
//     'AND tbl_suggest_exporting_items.warehouse_item_id IN (' . implode(',', $warehouse_id) . ')',
//     'AND tbl_suggest_exporting.status_stock is not NULL',
//     'AND tbl_suggest_exporting.warehouseman_id != 0',
// );
$where_exportsx = array(
    'AND tbl_suggest_exporting.status_stock is not NULL',
    'AND tbl_suggest_exporting.warehouseman_id != 0',
);
if (!empty($warehouse_id)) {
    $where_exportsx = array(
        'AND tbl_suggest_exporting_items.warehouse_item_id IN (' . implode(',', $warehouse_id) . ')',
        'AND tbl_suggest_exporting.status_stock is not NULL',
        'AND tbl_suggest_exporting.warehouseman_id != 0',
    );
}
if (!empty($type_items)) {
    array_push($where_exportsx, 'AND tbl_suggest_exporting_items.item_id =', $custom_item_select);
    array_push($where_exportsx, 'AND tbl_suggest_exporting_items.type_item LIKE "%' . $type_items . '%"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($where_exportsx, 'AND tbl_suggest_exporting.date_warehouseman >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($where_exportsx, 'AND tbl_suggest_exporting.date_warehouseman <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 1) {
    array_push($where_exportsx, 'AND tbl_suggest_exporting.id  = -1');
}
$aColumns_exportsx     = $select_exportsx;
$sIndexColumn_exportsx = "id";
$sTable_exportsx       = 'tbl_suggest_exporting_items';
$join_exportsx         = array(
    'LEFT JOIN tbl_suggest_exporting ON tbl_suggest_exporting.id = tbl_suggest_exporting_items.suggest_exporting_id',
);

$order_by_exportsx = 'order by item_id asc';
$result_exportsx   = data_tables_init($aColumns_exportsx, $sIndexColumn_exportsx, $sTable_exportsx, $join_exportsx, $where_exportsx, array('tbl_suggest_exporting.id as id_main,tbl_suggest_exporting_items.item_id as product_id,' . ch_where('tbl_suggest_exporting_items', 'type_item') . ' as type,tbl_suggest_exporting_items.location_id as localtion_id,id_import,8 as exists_quantity'));

$output_exportsx  = $result_exportsx['output'];
$rResult_exportsx = $result_exportsx['rResult'];
//nhapkho thành phẩm
$select_import_tp = array(
    'tbl_purchase_products.date_warehouseman as warehouseman_date',
    'tbl_purchase_products.date as date',
    'tbl_purchase_products.reference_no as code',
    'tbl_purchase_products.warehouse_id as warehouse_id',
    'tbl_purchase_products.note as reason',
    '1',
    'tbl_purchase_product_items.quantity as check_quantity'
);
// $where_import_tp = array(
//     'AND tbl_purchase_products.warehouse_id IN (' . implode(',', $warehouse_id) . ')',
//     'AND tbl_purchase_products.warehouseman_id != 0',
// );
$where_import_tp = array(
    'AND tbl_purchase_products.warehouseman_id != 0',
);
if (!empty($warehouse_id)) {
    $where_import_tp = array(
        'AND tbl_purchase_products.warehouse_id IN (' . implode(',', $warehouse_id) . ')',
        'AND tbl_purchase_products.warehouseman_id != 0',
    );
}
if (!empty($type_items)) {
    array_push($where_import_tp, 'AND tbl_purchase_product_items.item_id =', $custom_item_select);
    array_push($where_import_tp, 'AND tbl_purchase_product_items.type_item = "' . $type_items . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($where_import_tp, 'AND tbl_purchase_products.date_warehouseman >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($where_import_tp, 'AND tbl_purchase_products.date_warehouseman <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 2) {
    array_push($where_import_tp, 'AND tbl_purchase_products.id  = -1');
}
$aColumns_import_tp     = $select_import_tp;
$sIndexColumn_import_tp = "id";
$sTable_import_tp       = 'tbl_purchase_product_items';
$join_import_tp         = array(
    'LEFT JOIN tbl_purchase_products ON tbl_purchase_products.id = tbl_purchase_product_items.purchase_product_id',
);

$order_by_import_tp = 'order by item_id asc';
$result_import_tp   = data_tables_init($aColumns_import_tp, $sIndexColumn_import_tp, $sTable_import_tp, $join_import_tp, $where_import_tp, array('tbl_purchase_products.id as id_main,tbl_purchase_product_items.item_id as product_id,' . ch_where('tbl_purchase_product_items', 'type_item') . ' as type,tbl_purchase_product_items.location_id as localtion_id,tbl_purchase_products.productions_orders_details_id,9 as exists_quantity'));

$output_import_tp  = $result_import_tp['output'];
$rResult_import_tp = $result_import_tp['rResult'];


//trả lại hàng bán
$select_return_order = array(
    'tbl_returned_goods.warehouseman_date as warehouseman_date',
    'tbl_returned_goods.date as date',
    'tbl_returned_goods.reference_no as code',
    'tbl_returned_goods_items.warehouse_id as warehouse_id',
    'tbl_returned_goods.note as reason',
    '1',
    'tbl_returned_goods_items.quantity as check_quantity'
);
// $where_return_order = array(
//     'AND tbl_returned_goods_items.warehouse_id IN (' . implode(',', $warehouse_id) . ')',
//     'AND tbl_returned_goods.warehouseman_id != 0',
// );
$where_return_order = array(
    'AND tbl_returned_goods.warehouseman_id != 0',
);
if (!empty($warehouse_id)) {
    $where_return_order = array(
        'AND tbl_returned_goods_items.warehouse_id IN (' . implode(',', $warehouse_id) . ')',
        'AND tbl_returned_goods.warehouseman_id != 0',
    );
}
if (!empty($type_items)) {
    array_push($where_return_order, 'AND tbl_returned_goods_items.item_id =', $custom_item_select);
    array_push($where_return_order, 'AND tbl_returned_goods_items.type_item = "' . $type_items . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($where_return_order, 'AND tbl_returned_goods.warehouseman_date >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($where_return_order, 'AND tbl_returned_goods.warehouseman_date <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 2) {
    array_push($where_return_order, 'AND tbl_returned_goods.id  = -1');
}
$aColumns_return_order     = $select_return_order;
$sIndexColumn_return_order = "id";
$sTable_return_order       = 'tbl_returned_goods_items';
$join_return_order         = array(
    'LEFT JOIN tbl_returned_goods ON tbl_returned_goods.id = tbl_returned_goods_items.returned_goods_id',
);

$order_by_return_order = 'order by item_id asc';
$result_return_order   = data_tables_init($aColumns_return_order, $sIndexColumn_return_order, $sTable_return_order, $join_return_order, $where_return_order, array('tbl_returned_goods.id as id_main,tbl_returned_goods_items.item_id as product_id,' . ch_where('tbl_returned_goods_items', 'type_item') . ' as type,tbl_returned_goods_items.localtion_id as localtion_id,9999 as exists_quantity'));

$output_return_order  = $result_return_order['output'];
$rResult_return_order = $result_return_order['rResult'];






//nhapkho gia công
$select_import_gc = array(
    'tbl_import_outsource.warehouseman_date as warehouseman_date',
    'tbl_import_outsource.date as date',
    'tbl_import_outsource.reference_no as code',
    'tbl_import_outsource.warehouse_to as warehouse_id',
    'tbl_import_outsource.note as reason',
    '1',
    'tbl_import_outsource_items.quantity as check_quantity'
);
// $where_import_gc = array(
//     'AND tbl_import_outsource.warehouse_to IN (' . implode(',', $warehouse_id) . ')',
//     'AND tbl_import_outsource.warehouseman_id != 0',
// );
$where_import_gc = array(
    'AND tbl_import_outsource.warehouseman_id != 0',
);
if (!empty($warehouse_id)) {
    $where_import_gc = array(
        'AND tbl_import_outsource.warehouse_to IN (' . implode(',', $warehouse_id) . ')',
        'AND tbl_import_outsource.warehouseman_id != 0',
    );
}
if (!empty($type_items)) {
    array_push($where_import_gc, 'AND tbl_import_outsource_items.item_id =', $custom_item_select);
    array_push($where_import_gc, 'AND tbl_import_outsource_items.type_item = "' . $type_items . '"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($where_import_gc, 'AND tbl_import_outsource.warehouseman_date >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($where_import_gc, 'AND tbl_import_outsource.warehouseman_date <=' . '"' . $endMonth . ' 23:59:59"');
}
if ($id == 2) {
    array_push($where_import_gc, 'AND tbl_import_outsource.id  = -1');
}
$aColumns_import_gc     = $select_import_gc;
$sIndexColumn_import_gc = "id";
$sTable_import_gc       = 'tbl_import_outsource_items';
$join_import_gc         = array(
    'LEFT JOIN tbl_import_outsource ON tbl_import_outsource.id = tbl_import_outsource_items.import_outsource_id',
);

$order_by_import_gc = 'order by item_id asc';
$result_import_gc   = data_tables_init($aColumns_import_gc, $sIndexColumn_import_gc, $sTable_import_gc, $join_import_gc, $where_import_gc, array('tbl_import_outsource.id as id_main,tbl_import_outsource_items.item_id as product_id,' . ch_where('tbl_import_outsource_items', 'type_item') . ' as type,tbl_import_outsource_items.locaiton_to as localtion_id,897 as exists_quantity'));

$output_import_gc  = $result_import_gc['output'];
$rResult_import_gc = $result_import_gc['rResult'];

//nhapkho phe lieu
$select_import_pl = array(
    'tbl_purchase_internal.date_warehouseman as warehouseman_date',
    'tbl_purchase_internal.date as date',
    'tbl_purchase_internal.reference_no as code',
    'tbl_purchase_internal.warehouse_id as warehouse_id',
    'tbl_purchase_internal.note as reason',
    '1',
    'tbl_purchase_internal_items.quantity as check_quantity'
);
// $where_import_pl = array(
//     'AND tbl_purchase_internal.warehouse_id =' . $warehouse_id,
//     'AND tbl_purchase_internal.warehouseman_id != 0',
// );
$where_import_pl = array(
    'AND tbl_purchase_internal.warehouseman_id != 0',
);
if (!empty($warehouse_id)) {
    $where_import_pl = array(
        'AND tbl_purchase_internal.warehouse_id IN (' . implode(',', $warehouse_id) . ')',
        'AND tbl_purchase_internal.warehouseman_id != 0',
    );
}
if (!empty($type_items)) {
    array_push($where_import_pl, 'AND tbl_purchase_internal_items.item_id =', $custom_item_select);
    array_push($where_import_pl, 'AND tbl_purchase_internal_items.type_item LIKE "%' . $type_items . '%"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($where_import_pl, 'AND tbl_purchase_internal.date_warehouseman >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($where_import_pl, 'AND tbl_purchase_internal.date_warehouseman <=' . '"' . $endMonth . ' 23:59:59"');
}
$aColumns_import_pl     = $select_import_pl;
$sIndexColumn_import_pl = "id";
$sTable_import_pl       = 'tbl_purchase_internal_items';
$join_import_pl         = array(
    'INNER JOIN tbl_purchase_internal ON tbl_purchase_internal.id = tbl_purchase_internal_items.purchase_internal_id',
);

$order_by_import_pl = 'order by item_id asc';
$result_import_pl   = data_tables_init($aColumns_import_pl, $sIndexColumn_import_pl, $sTable_import_pl, $join_import_pl, $where_import_pl, array('tbl_purchase_internal.id as id_main,tbl_purchase_internal_items.item_id as product_id,' . ch_where('tbl_purchase_internal_items', 'type_item') . ' as type,tbl_purchase_internal_items.location_id as localtion_id,239 as exists_quantity'));

$output_import_pl  = $result_import_pl['output'];
$rResult_import_pl = $result_import_pl['rResult'];

$aColumnsG = array(
    'warehouseman_date',
    'date',
    'code',
    'warehouse_id',
    'reason',
    '1',
    'check_quantity'
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
if (!empty($rResult_import_tp)) {
    $rResultG = array_merge($rResultG, $rResult_import_tp);
}
if (!empty($rResult_return_order)) {
    $rResultG = array_merge($rResultG, $rResult_return_order);
}
if (!empty($rResult_export_different)) {
    $rResultG = array_merge($rResultG, $rResult_export_different);
}
if (!empty($rResult_import_gc)) {
    $rResultG = array_merge($rResultG, $rResult_import_gc);
}
if (!empty($rResult_import_pl)) {
    $rResultG = array_merge($rResultG, $rResult_import_pl);
}
if (!empty($rResultG)) {
    usort($rResultG, ch_make_cmp(['type' => "desc", 'product_id' => "desc", 'localtion_id' => "desc", 'warehouseman_date' => "asc"]));
}
$output = $outputimport;
$output['iTotalRecords'] = $outputimport['iTotalRecords'] + $outputexport['iTotalRecords'] + $output_return['iTotalRecords'] + $output_adjustedG['iTotalRecords']  + $output_TranfersN['iTotalRecords']  + $output_adjustedT['iTotalRecords']   + $output_exportsx['iTotalRecords'] + $output_TranfersD['iTotalRecords'] + $output_import_tp['iTotalRecords'] + $output_import_gc['iTotalRecords'] + $output_import_pl['iTotalRecords'];


$output['iTotalDisplayRecords'] = $outputimport['iTotalDisplayRecords'] + $outputexport['iTotalDisplayRecords'] + $output_return['iTotalDisplayRecords'] + $output_adjustedG['iTotalDisplayRecords'] + $output_adjustedT['iTotalDisplayRecords'] + $output_TranfersN['iTotalDisplayRecords']    + $output_TranfersD['iTotalDisplayRecords'] + $output_exportsx['iTotalDisplayRecords'] + $output_import_tp['iTotalDisplayRecords']  + $output_import_gc['iTotalDisplayRecords'] + $output_import_pl['iTotalDisplayRecords'];
$currentPage = $this->ci->input->post('start');
$sumFExistsQ = 0;
// $row= array();
foreach ($rResultG as $key => $aRow) {
    if ($key == 0) {
        $date = $aRow['product_id'];
        $type = $aRow['type'];
        $sumFExistsQall = getStartInventoryArray($aRow['product_id'], $aRow['type'], $warehouse_id, $beginMonth);
        $sumExistsQall = sumExistsQ_all_viewinventory($aRow['product_id'], $rResultG, $key, $aRow['type']);
        $get_items = get_items($aRow['product_id'], $aRow['type']);
        $row = array(
            $get_items->name . ' (' . $get_items->code . ') ' . format_item_purchases($aRow['type']),
            '',
            '',
            '',
            '',
            '<div class="text-center">' . $get_items->unit_name . '</div>',
            '<div class="text-center">' . formatNumber($sumExistsQall) . '</div>'
        );
        $row['DT_RowClass'] = 'alert-header bold warning';

        for ($i = 0; $i < count($aColumnsG); $i++) {
            $row[] = "";
        }
        $output['aaData'][] = $row;
        // $localtion = $aRow['localtion_id'];

        // $name = get_listname_localtion_warehouse($aRow['localtion_id']);
        // $sumFExistsQ_local = getStartInventoryArray($aRow['product_id'], $aRow['type'], $warehouse_id, $beginMonth, $aRow['localtion_id']);
        // $sumExistsQ_local = sumExistsQ_viewinventory($aRow['localtion_id'], $aRow['product_id'], $rResultG, $key, $aRow['type']) + $sumFExistsQ_local;
        // $row = array(
        //     _l('tnh_categories_capacity') . ': ' . $name,
        //     '',
        //     '',
        //     '',
        //     '',
        //     _l('inventory_begin') . ': ' . formatNumber($sumFExistsQ_local),
        //     _l('inventory_end') . ': ' . formatNumber($sumExistsQ_local)
        // );
        // if ($aRow['localtion_id'] != $localtion) {
        //     $name = get_listname_localtion_warehouse($aRow['localtion_id']);
        //     $sumFExistsQ_local = getStartInventoryArray($aRow['product_id'], $aRow['type'], $warehouse_id, $beginMonth, $aRow['localtion_id']);
        //     $sumExistsQ_local = sumExistsQ_viewinventory($aRow['localtion_id'], $aRow['product_id'], $rResultG, $key, $aRow['type']) + $sumFExistsQ_local;
        //     $row = array(
        //         _l('tnh_categories_capacity') . ': ' . $name,
        //         '',
        //         '',
        //         '',
        //         '',
        //         _l('inventory_begin') . ': ' . formatNumber($sumFExistsQ_local),
        //         _l('inventory_end') . ': ' . formatNumber($sumExistsQ_local)
        //     );
        //     $localtion = $aRow['localtion_id'];
        // }
        // $row['DT_RowClass'] = 'alert-header bold danger';

        // for ($i = 0; $i < count($aColumnsG); $i++) {
        //     $row[] = "";
        // }
        // $output['aaData'][] = $row;
    } else {

        if ($aRow['product_id'] != $date && $aRow['type'] == $type) {
            $date = $aRow['product_id'];
            $type = $aRow['type'];
            $sumFExistsQall = getStartInventoryArray($aRow['product_id'], $aRow['type'], $warehouse_id, $beginMonth);
            $sumExistsQall = ssumExistsQ_all_viewinventory_v2($aRow['product_id'], $rResultG, $key, $aRow['type']);
            $get_items = get_items($aRow['product_id'], $aRow['type']);
            $row = array(

                $get_items->name . ' (' . $get_items->code . ') ' . format_item_purchases($aRow['type']),
                '',
                '',
                '',
                '',
                '<div class="text-center">' . $get_items->unit_name . '</div>',
                '<div class="text-center">' . formatNumber($sumExistsQall) . '</div>'
            );
            $row['DT_RowClass'] = 'alert-header bold warning';

            for ($i = 0; $i < count($aColumnsG); $i++) {
                $row[] = "";
            }
            $output['aaData'][] = $row;

            // $localtion = $aRow['localtion_id'];

            // $name = get_listname_localtion_warehouse($aRow['localtion_id']);
            // $sumFExistsQ_local = getStartInventoryArray($aRow['product_id'], $aRow['type'], $warehouse_id, $beginMonth, $aRow['localtion_id']);
            // $sumExistsQ_local = sumExistsQ_viewinventory($aRow['localtion_id'], $aRow['product_id'], $rResultG, $key, $aRow['type']) + $sumFExistsQ_local;
            // $row = array(
            //     _l('tnh_categories_capacity') . ': ' . $name,
            //     '',
            //     '',
            //     '',
            //     '',
            //     _l('inventory_begin') . ': ' . formatNumber($sumFExistsQ_local),
            //     _l('inventory_end') . ': ' . formatNumber($sumExistsQ_local)
            // );
            // if ($aRow['localtion_id'] != $localtion) {
            //     $name = get_listname_localtion_warehouse($aRow['localtion_id']);
            //     $sumFExistsQ_local = getStartInventoryArray($aRow['product_id'], $aRow['type'], $warehouse_id, $beginMonth, $aRow['localtion_id']);
            //     $sumExistsQ_local = sumExistsQ_viewinventory($aRow['localtion_id'], $aRow['product_id'], $rResultG, $key, $aRow['type']) + $sumFExistsQ_local;
            //     $row = array(
            //         _l('tnh_categories_capacity') . ': ' . $name,
            //         '',
            //         '',
            //         '',
            //         '',
            //         _l('inventory_begin') . ': ' . formatNumber($sumFExistsQ_local),
            //         _l('inventory_end') . ': ' . formatNumber($sumExistsQ_local)
            //     );
            //     $localtion = $aRow['localtion_id'];
            // }
            // $row['DT_RowClass'] = 'alert-header bold danger';
            // for ($i = 0; $i < count($aColumnsG); $i++) {
            //     $row[] = "";
            // }
            // $output['aaData'][] = $row;
        } elseif ($aRow['product_id'] != $date && $aRow['type'] != $type) {
            $date = $aRow['product_id'];
            $type = $aRow['type'];
            $sumFExistsQall = getStartInventoryArray($aRow['product_id'], $aRow['type'], $warehouse_id, $beginMonth);
            $sumExistsQall = ssumExistsQ_all_viewinventory_v2($aRow['product_id'], $rResultG, $key, $aRow['type']);
            $get_items = get_items($aRow['product_id'], $aRow['type']);
            $row = array(

                $get_items->name . ' (' . $get_items->code . ') ' . format_item_purchases($aRow['type']),
                '',
                '',
                '',
                '',
                '<div class="text-center">' . $get_items->unit_name . '</div>',
                '<div class="text-center">' . formatNumber($sumExistsQall) . '</div>'
            );
            $row['DT_RowClass'] = 'alert-header bold warning';

            for ($i = 0; $i < count($aColumnsG); $i++) {
                $row[] = "";
            }
            $output['aaData'][] = $row;

            // $localtion = $aRow['localtion_id'];

            // $name = get_listname_localtion_warehouse($aRow['localtion_id']);
            // $sumFExistsQ_local = getStartInventoryArray($aRow['product_id'], $aRow['type'], $warehouse_id, $beginMonth, $aRow['localtion_id']);
            // $sumExistsQ_local = sumExistsQ_viewinventory($aRow['localtion_id'], $aRow['product_id'], $rResultG, $key, $aRow['type']) + $sumFExistsQ_local;
            // $row = array(
            //     _l('tnh_categories_capacity') . ': ' . $name,
            //     '',
            //     '',
            //     '',
            //     '',
            //     _l('inventory_begin') . ': ' . formatNumber($sumFExistsQ_local),
            //     _l('inventory_end') . ': ' . formatNumber($sumExistsQ_local)
            // );
            // if ($aRow['localtion_id'] != $localtion) {
            //     $name = get_listname_localtion_warehouse($aRow['localtion_id']);
            //     $sumFExistsQ_local = getStartInventoryArray($aRow['product_id'], $aRow['type'], $warehouse_id, $beginMonth, $aRow['localtion_id']);
            //     $sumExistsQ_local = sumExistsQ_viewinventory($aRow['localtion_id'], $aRow['product_id'], $rResultG, $key, $aRow['type']) + $sumFExistsQ_local;
            //     $row = array(
            //         _l('tnh_categories_capacity') . ': ' . $name,
            //         '',
            //         '',
            //         '',
            //         '',
            //         _l('inventory_begin') . ': ' . formatNumber($sumFExistsQ_local),
            //         _l('inventory_end') . ': ' . formatNumber($sumExistsQ_local)
            //     );
            //     $localtion = $aRow['localtion_id'];
            // }
            // $row['DT_RowClass'] = 'alert-header bold danger';
            // for ($i = 0; $i < count($aColumnsG); $i++) {
            //     $row[] = "";
            // }
            // $output['aaData'][] = $row;
        } else {
            // if ($aRow['localtion_id'] != $localtion) {
            //     $name = get_listname_localtion_warehouse($aRow['localtion_id']);
            //     $sumFExistsQ_local = getStartInventoryArray($aRow['product_id'], $aRow['type'], $warehouse_id, $beginMonth, $aRow['localtion_id']);
            //     $sumExistsQ_local = sumExistsQ_viewinventory($aRow['localtion_id'], $aRow['product_id'], $rResultG, $key, $aRow['type']) + $sumFExistsQ_local;
            //     $row = array(
            //         _l('tnh_categories_capacity') . ': ' . $name,
            //         '',
            //         '',
            //         '',
            //         '',
            //         _l('inventory_begin') . ': ' . formatNumber($sumFExistsQ_local),
            //         _l('inventory_end') . ': ' . formatNumber($sumExistsQ_local)
            //     );
            //     $localtion = $aRow['localtion_id'];
            //     $row['DT_RowClass'] = 'alert-header bold danger';

            //     for ($i = 0; $i < count($aColumnsG); $i++) {
            //         $row[] = "";
            //     }
            //     $output['aaData'][] = $row;
            // }
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
        if ($aColumnsG[$i] == 'check_quantity') {
            $_data = '<div class="text-center">' . formatNumber($aRow['check_quantity']) . '<div>';
        }
        if ($aColumnsG[$i] == '1') {
            $_data = '';
        }
        if ($aColumnsG[$i] == 'warehouse_id') {
            $_data = '';
            if (!empty($aRow['warehouse_id'])) {
                $warehous = get_table_where('tblwarehouse', array('id' => $aRow['warehouse_id']), '', 'row');
                if (!empty($warehous)) {
                    $_data = $warehous->name;
                }
            }
        }
        if ($aColumnsG[$i] == 'code') {
            if ($aRow['exists_quantity'] == 1) {
                $_data = '<a href="#" onclick="view_import(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-warning">' . _l('ch_importss') . '</span>';
            } elseif ($aRow['exists_quantity'] == 99) {
                $_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('releases/view_delivery/' . $aRow['id_main']) . '" data-toggle="modal" data-target="#myModal">' . $_data . '</a>  <span class="inline-block label label-info">' . _l('Giao hàng') . '</span>';
            } elseif ($aRow['exists_quantity'] == 3) {
                $_data = '<a href="#" onclick="view_return_suppliers(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_return_ncc') . '</span>';
            } elseif ($aRow['exists_quantity'] == 4) {
                $_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_adjustedG') . '</span>';
            } elseif ($aRow['exists_quantity'] == 5) {
                $_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_adjustedT') . '</span>';
            } elseif ($aRow['exists_quantity'] == 6) {
                $_data = '<a href="#" onclick="view_transfer(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_transfer_N') . '</span>';
            } elseif ($aRow['exists_quantity'] == 66) {
                $_data = '<a href="#" onclick="view_transfer(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_transfer_D') . '</span>';
            } elseif ($aRow['exists_quantity'] == 8) {
                $_data = '<a class="tnh-modal" title="Xem" data-tnh="modal" data-toggle="modal" data-target="#myModal" href="' . admin_url('stock/view_exporting_production/' . $aRow['id_main']) . '">' . $_data . '</a>  <span class="inline-block label label-success">' . _l('tnh_exporting_stock_producion') . '</span>';
            } elseif ($aRow['exists_quantity'] == 9) {
                $_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('stock/view_purchase_product/' . $aRow['id_main']) . '" data-toggle="modal" data-target="#myModal">' . $_data . '</a>  <span class="inline-block label label-success">' . _l('purchase_products') . '</span>';
            } elseif ($aRow['exists_quantity'] == 9999) {
                $_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('returned_goods/view_returned_goods/' . $aRow['id_main']) . '" data-toggle="modal" data-target="#myModal">' . $_data . '</a>  <span class="inline-block label label-success">' . _l('Trả lại hàng bán') . '</span>';
            } elseif ($aRow['exists_quantity'] == 888) {
                $_data = '<a href="#" onclick="view_export_different(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-success">' . _l('Xuất kho khác') . '</span>';
            } elseif ($aRow['exists_quantity'] == 897) {
                $_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('outsource/view_import_outsource/' . $aRow['id_main']) . '" data-toggle="modal" data-target="#myModal">' . $_data . '</a>  <span class="inline-block label label-danger">' . _l('Nhập gia công') . '</span>';
            } elseif ($aRow['exists_quantity'] == 239) {
                $_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('stock/view_purchase_internal/' . $aRow['id_main']) . '" data-toggle="modal" data-target="#myModal">' . $_data . '</a>  <span class="inline-block label label-info">' . _l('Thu hổi phiếu liệu') . '</span>';
            }
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
