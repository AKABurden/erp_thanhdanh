<?php
defined('BASEPATH') or exit('No direct script access allowed');
$months_report = $this->ci->input->post('report_months');
if ($months_report != '') {
    $custom_date_select = '';
    if (is_numeric($months_report)) {
        // Last month
        if ($months_report == '1') {
            $beginMonth = date('Y-m-01', strtotime('first day of last month'));
            $endMonth = date('Y-m-t', strtotime('last day of last month'));
        } else {
            $months_report = (int)$months_report;
            $months_report--;
            $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
            $endMonth = date('Y-m-t');
        }
    } elseif ($months_report == 'this_month') {
        $beginMonth = date('Y-m-01');
        $endMonth = date('Y-m-t');
    } elseif ($months_report == 'this_year') {
        $beginMonth = date('Y-m-d', strtotime(date('Y-01-01')));
        $endMonth = date('Y-m-d', strtotime(date('Y-12-31')));
    } elseif ($months_report == 'last_year') {
        $beginMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
        $endMonth = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
    } elseif ($months_report == 'custom') {
        $from_date = to_sql_date($this->ci->input->post('report_from'));
        $to_date = to_sql_date($this->ci->input->post('report_to'));
        if ($from_date == $to_date) {
            $beginMonth = $to_date;
            $endMonth = $to_date;
        } else {
            $beginMonth = $from_date;
            $endMonth = $to_date;
        }
    }
    // $this->db->where($custom_date_select);
}
$custom_item_select = $this->ci->input->post('custom_item_select');
$type_items = $this->ci->input->post('type_items');
$aColumns = array('tblitems.code as code_items', 'tblitems.name as name_items', 'tblunits.unit as unit', 'concat("items") as type', '3', '4', '5', '6', '7',);
$sIndexColumn = "id";
$sTable = 'tblitems';
$where = array();
if (!empty($type_items)) {
    if (($type_items == 'items') && ($type_items != 'product') && ($type_items != 'nvl') && ($type_items != 'tools')) {
        array_push($where, 'AND tblpurchase_order_items.product_id =', $custom_item_select);
    } else {
        array_push($where, 'AND tblpurchase_order_items.product_id =', 0);
    }
}
array_push($where, 'AND tblpurchase_order_items.type = "items"');
$group_by = 'GROUP BY tblitems.id';
$join = array('LEFT JOIN tblpurchase_order_items ON tblpurchase_order_items.product_id = tblitems.id ', 'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit',);
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tblitems.id as id_product'), $group_by);
$output = $result['output'];
$rResult = $result['rResult'];
$aColumns_pro = array('tbl_products.code as code_items', 'tbl_products.name as name_items', 'tblunits.unit as unit', 'concat("product") as type', '3', '4', '5', '6', '7',);
$sIndexColumn_pro = "id";
$sTable_pro = 'tbl_products';
$where_pro = array();
if (!empty($type_items)) {
    if (($type_items != 'items') && ($type_items == 'product') && ($type_items != 'nvl') && ($type_items != 'tools')) {
        array_push($where_pro, 'AND tblpurchase_order_items.product_id =', $custom_item_select);

        $this->ci->db->select(['tbl_products.code as code_items', 'tbl_products.name as name_items']);
        $get_items_search = $this->ci->db->get_where('tbl_products', ['id' => $custom_item_select])->row();

    } else {
        array_push($where_pro, 'AND tblpurchase_order_items.product_id =', 0);
    }
}
array_push($where_pro, 'AND tblpurchase_order_items.type = "product"');
$group_by_pro = 'GROUP BY tbl_products.id';
$join_pro = array('LEFT JOIN tblpurchase_order_items ON tblpurchase_order_items.product_id = tbl_products.id ', 'LEFT JOIN tblunits ON tblunits.unitid = tbl_products.unit_id',);
$result_pro = data_tables_init($aColumns_pro, $sIndexColumn_pro, $sTable_pro, $join_pro, $where_pro, array('tbl_products.id as id_product'), $group_by_pro);
$output_pro = $result_pro['output'];
$rResult_pro = $result_pro['rResult'];
$aColumns_nvl = array('tbl_materials.code as code_items', 'tbl_materials.name as name_items', 'tblunits.unit as unit', 'concat("nvl") as type', '3', '4', '5', '6', '7',);
$sIndexColumn_nvl = "id";
$sTable_nvl = 'tbl_materials';
$where_nvl = array();
if (!empty($type_items)) {
    if (($type_items != 'items') && ($type_items != 'product') && ($type_items == 'nvl') && ($type_items != 'tools')) {
        array_push($where_nvl, 'AND tblpurchase_order_items.product_id =', $custom_item_select);
        $this->ci->db->select(['tbl_materials.code as code_items', 'tbl_materials.name as name_items']);
        $get_items_search = $this->ci->db->get_where('tbl_materials', ['id' => $custom_item_select])->row();
    } else {
        array_push($where_nvl, 'AND tblpurchase_order_items.product_id =', 0);
    }
}
array_push($where_nvl, 'AND tblpurchase_order_items.type = "nvl"');
$group_by_nvl = 'GROUP BY tbl_materials.id';
$join_nvl = array('LEFT JOIN tblpurchase_order_items ON tblpurchase_order_items.product_id = tbl_materials.id ', 'LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.unit_payment',);
$result_nvl = data_tables_init($aColumns_nvl, $sIndexColumn_nvl, $sTable_nvl, $join_nvl, $where_nvl, array('tbl_materials.id as id_product'), $group_by_nvl);
$output_nvl = $result_nvl['output'];
$rResult_nvl = $result_nvl['rResult'];
$aColumns_tools = array('tbl_tools_supplies.code as code_items', 'tbl_tools_supplies.name as name_items', 'tblunits.unit as unit', 'concat("tools") as type', '3', '4', '5', '6', '7',);
$sIndexColumn_tools = "id";
$sTable_tools = 'tbl_tools_supplies';
$where_tools = array();
if (!empty($type_items)) {
    if (($type_items != 'items') && ($type_items != 'product') && ($type_items != 'nvl') && ($type_items == 'tools')) {
        array_push($where_tools, 'AND tblpurchase_order_items.product_id =', $custom_item_select);

        $this->ci->db->select(['tbl_tools_supplies.code as code_items', 'tbl_tools_supplies.name as name_items']);
        $get_items_search = $this->ci->db->get_where('tbl_tools_supplies', ['id' => $custom_item_select])->row();

    } else {
        array_push($where_tools, 'AND tblpurchase_order_items.product_id =', 0);
    }
}
array_push($where_tools, 'AND tblpurchase_order_items.type = "tools"');
$group_by_tools = 'GROUP BY tbl_tools_supplies.id';
$join_tools = array('LEFT JOIN tblpurchase_order_items ON tblpurchase_order_items.product_id = tbl_tools_supplies.id ', 'LEFT JOIN tblunits ON tblunits.unitid = tbl_tools_supplies.unit_id',);
$result_tools = data_tables_init($aColumns_tools, $sIndexColumn_tools, $sTable_tools, $join_tools, $where_tools, array('tbl_tools_supplies.id as id_product'), $group_by_tools);
$output_tools = $result_tools['output'];
$rResult_tools = $result_tools['rResult'];
$total = 0;
$promotion_expected = 0;
$j = 0;
$aColumnsG = array('code_items', 'name_items', 'unit', 'type', '3', '4', '5', '6', '7',);
$rResultG = array();
if (!empty($rResult)) {
    $rResultG = array_merge($rResultG, $rResult);
}
if (!empty($rResult_pro)) {
    $rResultG = array_merge($rResultG, $rResult_pro);
}
if (!empty($rResult_nvl)) {
    $rResultG = array_merge($rResultG, $rResult_nvl);
}
if (!empty($rResult_tools)) {
    $rResultG = array_merge($rResultG, $rResult_tools);
}
$output = $output;
$output['iTotalRecords'] = $output['iTotalRecords'] + $output_nvl['iTotalRecords'] + $output_pro['iTotalRecords'] + $output_tools['iTotalRecords'];
$output['iTotalDisplayRecords'] = $output['iTotalDisplayRecords'] + $output_nvl['iTotalDisplayRecords'] + $output_pro['iTotalDisplayRecords'] + $output_tools['iTotalDisplayRecords'];
$footer_data['total'] = 0;              //so luong mua
$footer_data['quantity'] = 0;           // gia tri mua
$footer_data['total_return'] = 0;       //so luong tra
$footer_data['quantity_return'] = 0;    // gia tri tra
$footer_data['promotion_expected'] = 0; // gia tri khuyen mai
$footer_data['subtotal'] = 0;           // tong tien
foreach ($rResultG as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumnsG); $i++) {
        if (strpos($aColumnsG[$i], 'as') !== false && !isset($aRow[$aColumnsG[$i]])) {
            $_data = $aRow[strafter($aColumnsG[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumnsG[$i]];
        }
        if ($aColumnsG[$i] == 'code_items') {
            $_data = $_data;
        }
        if ($aColumnsG[$i] == 'type') {
            $whereJoin = array();
            $whereJoin['where'] = array('tblpurchase_order_items.product_id' => $aRow['id_product'], 'tblpurchase_order_items.type' => $aRow['type'],);
            if (!empty($beginMonth) && !empty($endMonth)) {
                array_push($whereJoin['where'], 'tblpurchase_order.date >=' . '"' . $beginMonth . '"');
                array_push($whereJoin['where'], 'tblpurchase_order.date <=' . '"' . $endMonth . '"');
            }
            $whereJoin['join'] = array('tblpurchase_order,tblpurchase_order.id=id_purchase_order,left');
            $whereJoin['field'] = 'quantity_payment';
            $subtotal = sum_from_table_join('tblpurchase_order_items', $whereJoin);
            $footer_data['quantity'] += $subtotal;
            $_data = formatNumber($subtotal);
        }
        if ($aColumnsG[$i] == '3') {
            $whereJoin = array();
            $whereJoin['where'] = array('tblpurchase_order_items.product_id' => $aRow['id_product'], 'tblpurchase_order_items.type' => $aRow['type'],);
            if (!empty($beginMonth) && !empty($endMonth)) {
                array_push($whereJoin['where'], 'tblpurchase_order.date >=' . '"' . $beginMonth . '"');
                array_push($whereJoin['where'], 'tblpurchase_order.date <=' . '"' . $endMonth . '"');
            }
            $whereJoin['join'] = array('tblpurchase_order,tblpurchase_order.id=id_purchase_order,left');
            $whereJoin['field'] = 'total_suppliers';
            $subtotal = sum_from_table_join('tblpurchase_order_items', $whereJoin);
            $whereJoin1 = array();
            $whereJoin1['where'] = array('tblpurchase_order_items.product_id' => $aRow['id_product'], 'tblpurchase_order_items.type' => $aRow['type'],);
            $whereJoin1['join'] = array();
            $whereJoin1['field'] = 'promotion_expected';
            $subtotal1 = sum_from_table_join('tblpurchase_order_items', $whereJoin1);
            $_data = number_format($subtotal + $subtotal1);
            $total = $subtotal;
            $footer_data['total'] += $subtotal + $subtotal1;
            $_data = number_format($subtotal + $subtotal1);
        }
        if ($aColumnsG[$i] == '4') {
            $whereJoin = array();
            $whereJoin['where'] = array('tblreturn_suppliers_items.product_id' => $aRow['id_product'], 'tblreturn_suppliers_items.type' => $aRow['type'],);
            if (!empty($beginMonth) && !empty($endMonth)) {
                array_push($whereJoin['where'], 'tblreturn_suppliers.date >=' . '"' . $beginMonth . '"');
                array_push($whereJoin['where'], 'tblreturn_suppliers.date <=' . '"' . $endMonth . '"');
            }
            $whereJoin['join'] = array('tblreturn_suppliers,tblreturn_suppliers.id=id_return,left');
            $whereJoin['field'] = 'quantity_net';
            $subtotal = sum_from_table_join('tblreturn_suppliers_items', $whereJoin);
            $footer_data['quantity_return'] += $subtotal;
            $_data = formatNumber($subtotal);
        }
        if ($aColumnsG[$i] == '5') {
            $whereJoin = array();
            $whereJoin['where'] = array('tblreturn_suppliers_items.product_id' => $aRow['id_product'], 'tblreturn_suppliers_items.type' => $aRow['type'],);
            if (!empty($beginMonth) && !empty($endMonth)) {
                array_push($whereJoin['where'], 'tblreturn_suppliers.date >=' . '"' . $beginMonth . '"');
                array_push($whereJoin['where'], 'tblreturn_suppliers.date <=' . '"' . $endMonth . '"');
            }
            $whereJoin['join'] = array('tblreturn_suppliers,tblreturn_suppliers.id=id_return,left');
            $whereJoin['field'] = 'amount';
            $subtotal = sum_from_table_join('tblreturn_suppliers_items', $whereJoin);
            $promotion_expected = $subtotal;
            $footer_data['total_return'] += $subtotal;
            $_data = number_format($subtotal);
        }
        if ($aColumnsG[$i] == '6') {
            $whereJoin = array();
            $whereJoin['where'] = array('tblpurchase_order_items.product_id' => $aRow['id_product'], 'tblpurchase_order_items.type' => $aRow['type'],);
            $whereJoin['join'] = array();
            $whereJoin['field'] = 'promotion_expected';
            $subtotal = sum_from_table_join('tblpurchase_order_items', $whereJoin);
            $footer_data['promotion_expected'] += $subtotal;
            $_data = number_format($subtotal);
        }
        if ($aColumnsG[$i] == '7') {
            $footer_data['subtotal'] += ($total - $promotion_expected);
            $_data = number_format($total - $promotion_expected);
        }
        $row[] = $_data;
    }
    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = number_format($total);
    if ($key == 'quantity_return' || $key == 'quantity') {
        $footer_data[$key] = formatNumber($total);
    }
}
$output['sums'] = $footer_data;


$output['title_excel'] = [
    'Giai Đoạn : ' . (!empty($beginMonth) ? _dt($beginMonth) : '') .' - '. (!empty($endMonth) ? _dt($endMonth) : ''),
    'Mặt hàng: ' . (!empty($get_items_search) ? ($get_items_search->code_items.' ('.$get_items_search->name_items.')') : '')
];
