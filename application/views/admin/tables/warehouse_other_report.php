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
}


$warehouse_id = $this->ci->input->post('warehouse_id_array');
$custom_item_select = $this->ci->input->post('custom_item_select');
$type_items = $this->ci->input->post('type_items');


//Xuất kho sản xuất
$select_exportsx = array(
    '3',
    'tblexport_different.date as date',
    'CONCAT(tblexport_different.prefix,"-",tblexport_different.code) as code',
    '1',
    '2',
    '4',
    'tblwarehouse.name as name_warehouse',
    'tbltblexport_different_items.localtion_warehouses_id as localtion_id',
    'tbltblexport_different_items.quantity_net as quantity',
    'tblexport_different.note as note'
);
$where_exportsx = array(
    'AND tblexport_different.warehouseman_id != 0',
);
if (!empty($warehouse_id)) {
    array_push($where_exportsx, 'AND tbltblexport_different_items.warehouses_id IN (' . implode(',', $warehouse_id) . ')');
}
if (!empty($type_items)) {

    array_push($where_exportsx, 'AND tbltblexport_different_items.product_id =', $custom_item_select);
    array_push($where_exportsx, 'AND tbltblexport_different_items.type LIKE "%' . $type_items . '%"');
}
if (!empty($beginMonth) && !empty($endMonth)) {
    array_push($where_exportsx, 'AND tblexport_different.date >=' . '"' . $beginMonth . ' 00:00:00"');
    array_push($where_exportsx, 'AND tblexport_different.date <=' . '"' . $endMonth . ' 23:59:59"');
}
$category_search = $this->ci->input->post('category_search_new');

if (!empty($category_search)) {
    $category_search_nvl = array();
    $category_search_tools = array();
    foreach ($category_search as $key => $value) {
        $check_value = explode('_', $value);
        if ($check_value[0] == 'nvl') {
            $category_search_nvl[] = $check_value[1];
        }
        if ($check_value[0] == 'product') {
            $category_search_tools[] = $check_value[1];
        }
    }

    if (!empty($category_search_nvl) && !empty($category_search_tools)) {
        array_push($where_exportsx, 'AND (tbl_materials.category_id IN (' . implode(',', $category_search_nvl) . ') or tbl_products.category_id IN (' . implode(',', $category_search_tools) . '))');
    } else
    if (!empty($category_search_nvl) && empty($category_search_tools)) {
        array_push($where_exportsx, 'AND (tbl_materials.category_id IN (' . implode(',', $category_search_nvl) . '))');
    } elseif (empty($category_search_nvl) && !empty($category_search_tools)) {
        array_push($where_exportsx, 'AND (tbl_products.category_id IN (' . implode(',', $category_search_tools) . '))');
    }
}


$aColumns_exportsx     = $select_exportsx;
$sIndexColumn_exportsx = "id";
$sTable_exportsx       = 'tbltblexport_different_items';
$join_exportsx         = array(
    'LEFT JOIN tblexport_different ON tblexport_different.id = tbltblexport_different_items.id_export_different',
    'LEFT JOIN tblwarehouse ON tblwarehouse.id = tbltblexport_different_items.warehouses_id',
    'LEFT JOIN tbl_materials ON tbl_materials.id = tbltblexport_different_items.product_id and tbltblexport_different_items.type ="nvl"',
    'LEFT JOIN tbl_category_items ON tbl_category_items.id = tbl_materials.category_id',
    'LEFT JOIN tbl_products ON tbl_products.id = tbltblexport_different_items.product_id and tbltblexport_different_items.type = "product"',
    'LEFT JOIN tbl_category_products ON tbl_category_products.id = tbl_products.category_id',
);
$order_by_exportsx = 'order by product_id asc';
$result_exportsx   = data_tables_init($aColumns_exportsx, $sIndexColumn_exportsx, $sTable_exportsx, $join_exportsx, $where_exportsx, array('tblexport_different.id as id_main,tbltblexport_different_items.product_id as product_id,tbltblexport_different_items.type as type,5 as exists_quantity,tbl_category_items.name as name_cate,tbl_category_products.name as name_cate_product'));

$output_exportsx  = $result_exportsx['output'];
$rResult_exportsx = $result_exportsx['rResult'];

$aColumnsG = array(
    '3',
    'date',
    'code',
    '1',
    '2',
    '4',
    'name_warehouse',
    'localtion_id',
    'quantity',
    'note'
);
$rResultG = array();
if (!empty($rResultexport)) {
    $rResultG = array_merge($rResultG, $rResultexport);
}
if (!empty($rResult_return)) {
    $rResultG = array_merge($rResultG, $rResult_return);
}
if (!empty($rResult_adjustedG)) {
    $rResultG = array_merge($rResultG, $rResult_adjustedG);
}
if (!empty($rResult_TranfersD)) {
    $rResultG = array_merge($rResultG, $rResult_TranfersD);
}
if (!empty($rResult_exportsx)) {
    $rResultG = array_merge($rResultG, $rResult_exportsx);
}
if (!empty($rResultG)) {
    // usort($rResultG, ch_make_cmp(['type' => "desc",'product_id' => "desc",'localtion_id' => "desc",'warehouseman_date' => "asc",'exists_quantity'=> "asc"]));
}
$output = $output_exportsx;
// $output['iTotalRecords']=$outputexport['iTotalRecords'] +$output_return['iTotalRecords'] + $output_adjustedG['iTotalRecords']  + $output_exportsx['iTotalRecords'] + $output_TranfersD['iTotalRecords'];
// $output['iTotalDisplayRecords']=$outputexport['iTotalDisplayRecords'] + $output_return['iTotalDisplayRecords'] + $output_adjustedG['iTotalDisplayRecords']  + $output_exportsx['iTotalDisplayRecords'] + $output_TranfersD['iTotalDisplayRecords'];

$output['iTotalRecords'] = $output_exportsx['iTotalRecords'];
$output['iTotalDisplayRecords'] = $output_exportsx['iTotalDisplayRecords'];
$footer_data = array(
    'all' => 0,
);
$currentPage = $this->ci->input->post('start');
$sumFExistsQ = 0;
// $row= array();
foreach ($rResultG as $key => $aRow) {
    $row = [];
    $get_items = get_items($aRow['product_id'], $aRow['type']);
    for ($i = 0; $i < count($aColumnsG); $i++) {

        $_data = $aRow[$aColumnsG[$i]];
        if ($aColumnsG[$i] == '3') {
            if ($aRow['type'] == 'nvl') {
                $_data = '<div class="text-center">' . $aRow['name_cate'] . '<div>';
            } else {
                $_data = '<div class="text-center">' . $aRow['name_cate_product'] . '<div>';
            }
        }
        if ($aColumnsG[$i] == 'date') {
            $_data = '<div class="text-center">' . _d($aRow['date']) . '<div>';
        }
        if ($aColumnsG[$i] == 'quantity') {
            $_data = '<div class="text-center">' . formatNumber($aRow['quantity']) . '<div>';
            $footer_data['all'] += $aRow['quantity'];
        }
        if ($aColumnsG[$i] == '1') {
            $_data = $get_items->code;
        }
        if ($aColumnsG[$i] == '2') {
            $_data = $get_items->name;
        }
        if ($aColumnsG[$i] == '4') {
            $_data = $get_items->unit_name_stock;
        }
        if ($aColumnsG[$i] == 'localtion_id') {
            $_data = '<div class="text-center">' . get_listname_localtion_warehouse($aRow['localtion_id']) . '<div>';
        }
        if ($aColumnsG[$i] == 'code') {
            if ($aRow['exists_quantity'] == 1) {
                $_data = '<a data-tnh="modal" class="tnh-modal" href="' . admin_url('releases/view_export_warehouse/' . $aRow['id_main']) . '" data-toggle="modal" data-target="#myModal">' . $_data . '</a>  <span class="inline-block label label-info">' . _l('tnh_export_warehouse_sales') . '</span>';
            } elseif ($aRow['exists_quantity'] == 2) {
                $_data = '<a href="#" onclick="view_return_suppliers(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_return_ncc') . '</span>';
            } elseif ($aRow['exists_quantity'] == 3) {
                $_data = '<a href="#" onclick="view_adjusted(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_adjustedG') . '</span>';
            } elseif ($aRow['exists_quantity'] == 4) {
                $_data = '<a href="#" onclick="view_transfer(' . $aRow['id_main'] . '); return false;" >' . $_data . '</a>  <span class="inline-block label label-info">' . _l('ch_transfer_D') . '</span>';
            } elseif ($aRow['exists_quantity'] == 5) {
                $_data = '<a href="#" onclick="view_export_different(' . $aRow['id_main'].'); return false;" >'.$_data.'</a>' ;
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
