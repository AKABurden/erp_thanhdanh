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


$warehouse_id = $this->ci->input->post('warehouse_limit_date');
$type_limit_date = $this->ci->input->post('type_limit_date');
$custom_item_select = $this->ci->input->post('custom_item_select');
$type_items = $this->ci->input->post('type_items');
$limit_date = get_option('limit_date');
if (empty($limit_date)) {
    $limit_date = 0;
}

$time_form_check = date('Y-m-d');
$time_form = strtotime($time_form_check . " +$limit_date days");
$time_form = strftime("%Y-%m-%d", $time_form);
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
$aColumns = [
    'tblwarehouse_product.id',
    'tblwarehouse_product.type_items',
    '1',
    'tblwarehouse.name',
    'tbllocaltion_warehouses.name',
    'tblwarehouse_product.date_warehouse',
    '8',
    'tblwarehouse_product.date_sd',
    'tblwarehouse_product.quantity_left',
    '6',
    'IF(object_item_type = "orders",price_order,prices) as prices',
    // 'price_order as total_price'
    '(IF(object_item_type = "orders",price_order,prices) * tblwarehouse_product.quantity_left) as total_price'
];
$sIndexColumn = 'id';
$sTable       = 'tblwarehouse_product';

$join         = array(
    'LEFT JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_product.localtion',
    'LEFT JOIN tbltype_items ON tbltype_items.type = tblwarehouse_product.type_items',
    'LEFT JOIN tblwarehouse ON tblwarehouse.id = tblwarehouse_product.warehouse_id',
    'LEFT JOIN ' . $prices . ' ON prices.product_id = tblwarehouse_product.product_id AND prices.product_type = tblwarehouse_product.type_items',
    'LEFT JOIN ' . $localtion_warehouses . ' ON localtion_warehouses.localtion_warehousesid = tblwarehouse_product.localtion',

);
$where = array(
    'AND tblwarehouse_product.date_sd <= "' . $time_form . '"',
    'AND tblwarehouse_product.quantity_left > 0',
    'AND tblwarehouse_product.date_sd is NOT NULL',
);
array_push($where, 'AND tblwarehouse_product.type_items = "product"');

if (!empty($custom_item_select)) {
    array_push($where, 'AND tblwarehouse_product.product_id =', $custom_item_select);
    // array_push($where, 'AND tblwarehouse_product.type_items = "' . $type_items . '"');
}
if (!empty($warehouse_id)) {
    array_push($where, 'AND tblwarehouse_product.warehouse_id =', $warehouse_id);
}

if (!empty($type_limit_date)) {
    if ($type_limit_date == 1) {
        array_push($where, 'AND (tblwarehouse_product.date_sd > "' . $time_form_check . '" AND tblwarehouse_product.date_sd <= "' . $time_form . '")');
    } elseif ($type_limit_date == 2) {
        array_push($where, 'AND (tblwarehouse_product.date_sd < "' . $time_form_check . '")');
    }
}
// $group_by = "GROUP BY tblwarehouse_product.product_id,tblwarehouse_product.type_items,tblwarehouse_product.date_warehouse,tblwarehouse_product.date_sd";
$group_by = "GROUP BY tblwarehouse_product.id";
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblwarehouse_product.product_id', 'tbltype_items.name as name_type', 'type_export', 'import_id', 'lot_code', 'date_sd', 'date_sx', 'date_use'], $group_by);
$output  = $result['output'];
$rResult = $result['rResult'];

usort($rResult, ch_make_cmp(['tblwarehouse_product.type_items' => "desc", 'product_id' => "desc"]));
$currentPage = $this->_instance->input->post('start');
$currentall = $output['iTotalRecords'];
$footer_data = array(
    'product_quantity' => 0,
    'product_total' => 0,
);
foreach ($rResult as $r => $aRow) {
    $row = [];
    $get_items = get_items($aRow['product_id'], $aRow['tblwarehouse_product.type_items']);
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'tblwarehouse_product.id') {
            $_data = $get_items->code . '<br>';
        }
        if ($aColumns[$i] == 'tblwarehouse_product.date_sd') {
            $_data = '<div class="text-center">' . _d($aRow['tblwarehouse_product.date_sd']) . '</div>';
        }
        if ($aColumns[$i] == 'IF(object_item_type = "orders",price_order,prices) as prices') {
            $_data = '<div class="text-right">' . formatNumber($aRow['prices']) . '</div>';
        }
        if ($aColumns[$i] == '(IF(object_item_type = "orders",price_order,prices) * tblwarehouse_product.quantity_left) as total_price') {
            $_data = '<div class="text-right">' . formatNumber($aRow['total_price']) . '</div>';
            $footer_data['product_total'] += $aRow['total_price'];
        }
        if ($aColumns[$i] == 'tblwarehouse_product.quantity_left') {
            $footer_data['product_quantity'] += $aRow['tblwarehouse_product.quantity_left'];
            $_data = '<div class="text-center">' . formatNumber($aRow['tblwarehouse_product.quantity_left']) . '</div>';
        }
        if ($aColumns[$i] == 'tblwarehouse_product.date_warehouse') {
            $date_warehouse = $aRow['tblwarehouse_product.date_warehouse'];
            if ($aRow['type_export'] == 1 || $aRow['type_export'] == 3 || $aRow['type_export'] == 20) {
                $_data = '<div class="text-center">' . _dhau($date_warehouse) . '</div>';
            } else {
                if ($aRow['type_export'] == 2) {
                    if (!empty($aRow['import_id'])) {
                        $transfer =  get_table_where('tbltransfer_warehouse_detail', array('id_transfer' => $aRow['import_id'], 'id_items' => $aRow['product_id'], 'type' => $aRow['tblwarehouse_product.type_items'], 'lot_code' => $aRow['lot_code'], 'date_sx' => $aRow['date_sx'], 'date_sd' => $aRow['date_sd'], 'date_use' => $aRow['date_use']), '', 'row');
                        if (!empty($transfer)) {
                            if (!empty($transfer->id_import)) {
                                $id_import = explode('-', $transfer->id_import);
                                $check_warehous =  get_table_where('tblwarehouse_product', array('id' => $id_import[0]), '', 'row');
                                if ($check_warehous->type_export == 1  || $check_warehous->type_export == 3 || $check_warehous->type_export == 20) {
                                    $date_warehouse = $check_warehous->date_warehouse;
                                }
                            }
                        }
                    }
                    $_data = '<div class="text-center">' . _dhau($date_warehouse) . '</div>';
                } else {
                    $_data = '<div class="text-center"></div>';
                }
            }
        }
        if ($aColumns[$i] == '8') {
            $day_1 = explode(' ', $date_warehouse)[0];
            $day_2 = date('Y-m-d'); //current date
            $days = (strtotime($day_2) - strtotime($day_1)) / (60 * 60 * 24);
            $_data = '<div class="text-center">' . formatNumber($days) . '</div>';
        }
        if ($aColumns[$i] == 'tblwarehouse_product.type_items') {
            $_data = $get_items->name;
        }
        if ($aColumns[$i] == '1') {
            $_data = '<div class="text-center">' . $get_items->unit_name_stock . '<div>';
        }
        if ($aColumns[$i] == '6') {
            if ($aRow['tblwarehouse_product.date_sd'] < $time_form_check) {
                $_data = '<div class="text-center"><span class="inline-block label label-danger">Quá hạn</span></div>';
            } elseif ($aRow['tblwarehouse_product.date_sd'] > $time_form_check && $aRow['tblwarehouse_product.date_sd'] <= $time_form) {
                $_data = '<div class="text-center"><span class="inline-block label label-warning">Sắp đến hạn</span></div>';
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
