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


$warehouse_id = $this->ci->input->post('warehouse_id');
$custom_item_select = $this->ci->input->post('custom_item_select');
$type_items = $this->ci->input->post('type_items');
$filterStatus_v2 = $this->ci->input->post('filterStatus_v2');

$data_warehouse = get_table_where('tblwarehouse');

$aColumns = [
    'tbl_products.code',
    'tbl_products.name',
    'tblunits.unit',
    '2',
    'tbllocaltion_warehouses.name as name_local'
];
foreach ($data_warehouse as $value) {
    array_push($aColumns, $value['id'] . ' as warehouse_' . $value['id']);
    $footer_data['warehouse_' . $value['id']] = 0;
};
array_push($aColumns, 'SUM(tblwarehouse_items.product_quantity) as qty');
// array_push($aColumns, '1');
$sIndexColumn = 'id';
$sTable       = 'tblwarehouse_items';

$join         = array(
    'LEFT JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_items.localtion',
    'LEFT JOIN tbl_products ON tbl_products.id = id_items',
    'LEFT JOIN tbl_productions_orders_details ON tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id',
    'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.conversion_unit',
);
$where = array(
    'AND tblwarehouse_items.type_items = "product"',
);
// if ($filterStatus_v2 == 2) {
//     array_push($where, 'AND tbl_productions_orders_details.object_type = "orders"');
// } elseif ($filterStatus_v2 == 1) {
//     array_push($where, 'AND (tbl_productions_orders_details.object_type != "orders" or tbllocaltion_warehouses.pod_id = 0)');
// }
// if ($filterStatus_v2 == 2) {
//     array_push($where, 'AND tbllocaltion_warehouses.order_id > 0');
// } elseif ($filterStatus_v2 == 1) {
//     array_push($where, 'AND tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.stage_id = 0');
// }
if ($filterStatus_v2 == 2) {
    array_push($where, 'AND (tbllocaltion_warehouses.order_id > 0 OR (tbl_productions_orders_details.object_type = "orders"  AND tbllocaltion_warehouses.stage_id = 0))');
} elseif ($filterStatus_v2 == 1) {
    array_push($where, 'AND ((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != "orders"  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))');
}
if ($this->ci->input->post('products_search')) {
    $products_search = $this->ci->input->post('products_search');
    $products_search = explode('__', $products_search);
    array_push($where, 'AND tblwarehouse_items.id_items =' . $products_search[0]);
}
if ($this->ci->input->post('category_search_products')) {
    array_push($where, 'AND tbl_products.category_id =' . $this->ci->input->post('category_search_products'));
}
$group_by = "GROUP BY tblwarehouse_items.id_items,tblwarehouse_items.lot_code,date_sx,date_sd,date_use,tblwarehouse_items.localtion";
$result  = data_tables_init_having($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblwarehouse_items.id_items', 'tblwarehouse_items.warehouse_id','tblwarehouse_items.lot_code','tblwarehouse_items.date_sx','tblwarehouse_items.date_sd','tblwarehouse_items.date_use','tblwarehouse_items.localtion'], '', $group_by, 'having qty > 0');
$output  = $result['output'];
$rResult = $result['rResult'];
$footer_data['all'] = 0;
// echo "<pre>";
// print_r($result);die();
// usort($rResult, ch_make_cmp(['tblwarehouse_product.type_items' => "desc", 'product_id' => "desc"]));
$currentPage = $this->_instance->input->post('start');
$currentall = $output['iTotalRecords'];
foreach ($rResult as $r => $aRow) {
    $row = [];
    // $get_items = get_items($aRow['product_id'],$aRow['tblwarehouse_product.type_items']);
    // $sumFExistsQall=getStartInventory($aRow['product_id'],$aRow['tblwarehouse_product.type_items'],$warehouse_id,$beginMonth);
    // $sumFExistsQall_import=getStartInventory_import($aRow['product_id'],$aRow['tblwarehouse_product.type_items'],$warehouse_id,$beginMonth,$endMonth);
    // $sumFExistsQall_export=getStartInventory_export($aRow['product_id'],$aRow['tblwarehouse_product.type_items'],$warehouse_id,$beginMonth,$endMonth);
    $qty = 0;

    for ($i = 0; $i < count($aColumns); $i++) {
        // $_data='';
        if ($aColumns[$i] == 'tbl_products.code') {
            $_data = $aRow['tbl_products.code'];
        }
        if ($aColumns[$i] == 'tbl_products.name') {
            $_data = $aRow['tbl_products.name'];
        }
        if ($aColumns[$i] == 'tblunits.unit') {
            $_data = $aRow['tblunits.unit'];
        }
        if ($aColumns[$i] == 'tbllocaltion_warehouses.name as name_local') {
            $_data = $aRow['name_local'];
        }
        if ($aColumns[$i] == '2') {
            $html = '<div><span>' . _l('Lot') . ':' . $aRow['lot_code'] . '</span><br>
                    <span>' . _l('ch_date_of_manufacture') . ':' . _d($aRow['date_sx']) . '</span><br>
                    <span>' . _l('ch_items_dateed') . ':' . _d($aRow['date_sd']) . '</span><br>
                    <span>' . _l('ch_items_date_use') . ':' . $aRow['date_use'] . '</span></div>';
            $_data = $html;
        }
        foreach ($data_warehouse as $value) {
            if ($aColumns[$i] == $value['id'] . ' as warehouse_' . $value['id']) {
                $whereJoin = array();
                $whereJoin['where'] = array(
                    'tblwarehouse_items.type_items' => "product",
                    'tblwarehouse_items.warehouse_id' => $value['id'],
                    'tblwarehouse_items.id_items' => $aRow['id_items'],
                    'tblwarehouse_items.lot_code' => $aRow['lot_code'],
                    'tblwarehouse_items.date_sx' => $aRow['date_sx'],
                    'tblwarehouse_items.date_sd' => $aRow['date_sd'],
                    'tblwarehouse_items.date_use' => $aRow['date_use'],
                    'tblwarehouse_items.localtion' => $aRow['localtion'],
                );
                // if ($filterStatus_v2 == 2) {
                //     $whereJoin['where'][] = 'tbllocaltion_warehouses.order_id > 0';
                // } elseif ($filterStatus_v2 == 1) {
                //     $whereJoin['where'][] = 'tbllocaltion_warehouses.order_id = 0  AND tbllocaltion_warehouses.stage_id = 0';
                // }
                if ($filterStatus_v2 == 2) {
                    $whereJoin['where'][] = '(tbllocaltion_warehouses.order_id > 0 OR (tbl_productions_orders_details.object_type = "orders"  AND tbllocaltion_warehouses.stage_id = 0))';
                } elseif ($filterStatus_v2 == 1) {
                    $whereJoin['where'][] = '((tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != "orders"  AND tbllocaltion_warehouses.stage_id = 0)  or (tbllocaltion_warehouses.order_id = 0 AND tbllocaltion_warehouses.pod_id = 0   AND tbllocaltion_warehouses.stage_id = 0))';
                }
    //             if ($filterStatus_v2 == 2) {
    //     array_push($where, 'AND (tbllocaltion_warehouses.order_id > 0 OR (tbl_productions_orders_details.object_type = "orders"  AND tbllocaltion_warehouses.stage_id = 0))');
    // } elseif ($filterStatus_v2 == 1) {
    //     array_push($where, 'AND (tbllocaltion_warehouses.order_id = 0 AND tbl_productions_orders_details.object_type != "orders"  AND tbllocaltion_warehouses.stage_id = 0)');
    // }
                $whereJoin['join'] = array(
                    'tbllocaltion_warehouses,tbllocaltion_warehouses.id = tblwarehouse_items.localtion,LEFT',
                    'tbl_productions_orders_details,tbl_productions_orders_details.id = tbllocaltion_warehouses.pod_id,LEFT'
                );
                $whereJoin['field'] = 'product_quantity';
                $subtotal = sum_from_table_join('tblwarehouse_items', $whereJoin);
                // var_dump($this->ci->db->last_query());die();
                //                 $qty+=$subtotal;
                if ($subtotal > 0) {
                    $_data = '<div class="text-center">' . number_format($subtotal) . '</div>';
                } else {
                    $_data = '<div class="text-center">' . "-" . '</div>';
                }
                $footer_data['warehouse_' . $value['id']] += $subtotal;
                $footer_data['all'] += $subtotal;
            }
        }
        if ($aColumns[$i] == 'SUM(tblwarehouse_items.product_quantity) as qty') {
            $_data = "<div class='text-center' style='font-size:20px'>" . number_format($aRow['qty']) . "</div>";
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = number_format($total);
}
$output['sums']              = $footer_data;
