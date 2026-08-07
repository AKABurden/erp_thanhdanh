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

$data_warehouse = get_table_where('tblwarehouse');

$aColumns = [
    'tbl_materials.code',
    'tbl_materials.name',
    'tblunits.unit'
];
foreach ($data_warehouse as $value) {
    array_push($aColumns, $value['id'].' as warehouse_'.$value['id']);
    $footer_data['warehouse_'.$value['id']] = 0;
};
array_push($aColumns, 'SUM(tblwarehouse_items.product_quantity) as qty');
// array_push($aColumns, '1');
$sIndexColumn = 'id';
$sTable       = 'tblwarehouse_items';

$join         = array(
    'LEFT JOIN tbl_materials ON tbl_materials.id = id_items',
    'LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.standard_unit',
);
$where = array(
    'AND tblwarehouse_items.type_items = "nvl"',
);
if ($this->ci->input->post('material_id')) {
    array_push($where, 'AND tblwarehouse_items.id_items =' .$this->ci->input->post('material_id'));
}
if ($this->ci->input->post('category_id')) {
    array_push($where, 'AND tbl_materials.category_id =' .$this->ci->input->post('category_id'));
}
$group_by = "GROUP BY tblwarehouse_items.id_items";
$result  = data_tables_init_having($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblwarehouse_items.id_items', 'tblwarehouse_items.warehouse_id'], '',$group_by,'having qty > 0');
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
        if ($aColumns[$i] == 'tbl_materials.code') {
            $_data = $aRow['tbl_materials.code'];
        }
        if ($aColumns[$i] == 'tbl_materials.name') {
            $_data = $aRow['tbl_materials.name'];
        }
        if ($aColumns[$i] == 'tblunits.unit') {
            $_data = $aRow['tblunits.unit'];
        }
        foreach ($data_warehouse as $value) {
            if ($aColumns[$i] == $value['id'].' as warehouse_'.$value['id']) {
                $whereJoin = array();
                $whereJoin['where'] = array(
                    'tblwarehouse_items.type_items' => "nvl",
                    'tblwarehouse_items.warehouse_id' => $value['id'],
                    'tblwarehouse_items.id_items' => $aRow['id_items'],
                );
                $whereJoin['join'] = array();
                $whereJoin['field'] = 'product_quantity';
                $subtotal = sum_from_table_join('tblwarehouse_items', $whereJoin);
                // var_dump($this->ci->db->last_query());die();
//                 $qty+=$subtotal;
                if($subtotal>0){
                    $_data = '<div class="text-center">'.number_format($subtotal).'</div>';
                }else{
                    $_data = '<div class="text-center">'."-".'</div>';
                }
                $footer_data['warehouse_'.$value['id']] += $subtotal;
                $footer_data['all'] += $subtotal;
            }
        }
        if($aColumns[$i]=='SUM(tblwarehouse_items.product_quantity) as qty')
        {
            $_data = "<div class='text-center' style='font-size:20px'>".number_format($aRow['qty'])."</div>";
        }
        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key]=number_format($total);
}
$output['sums']              = $footer_data;