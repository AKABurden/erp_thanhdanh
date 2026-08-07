<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    '1',
    'tbl_materials.code',
    'tbl_materials.name',
    'tbllocaltion_warehouses.name as name_local',
    'SUM(tblwarehouse_product.quantity_left) as quantity',
    'tblunits.unit',
    'SUM(tblwarehouse_product.product_quantity_payment_left*tblwarehouse_product.price) as price',
    'tblwarehouse_product.lot_code as lot_code',
    'tblwarehouse_product.date_warehouse',
    '2',
    'tblwarehouse_product.date_sd as date_sd',
    'tbl_species.name as species_name',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'warehouse_product';

$join         = array(
    'LEFT JOIN tbl_materials ON tbl_materials.id = tblwarehouse_product.product_id',
    'LEFT JOIN tblunits ON tblunits.unitid = tbl_materials.standard_unit',
    'LEFT JOIN tbl_species ON tbl_species.id = tbl_materials.species',
    'LEFT JOIN tbllocaltion_warehouses ON tbllocaltion_warehouses.id = tblwarehouse_product.localtion',
);
$where = array();
array_push($where, 'AND tblwarehouse_product.warehouse_id NOT IN('.WAREHOUSES_SYSTEM.')');

if ($this->ci->input->post('filterStatus')) {
    array_push($where, 'AND tblwarehouse_product.type_items = "' . $this->ci->input->post('filterStatus') . '"');
}
array_push($where, 'AND tblwarehouse_product.type_items = "nvl"');

if ($this->ci->input->post('material_id_hs')) {
    array_push($where, 'AND tblwarehouse_product.product_id = ' . $this->ci->input->post('material_id_hs'));
}
if ($this->ci->input->post('lot_code')) {
    array_push($where, 'AND tblwarehouse_product.lot_code = ' . $this->ci->input->post('lot_code'));
}
$search = $this->ci->input->post('search')['value'];
if (!empty($search)) {
    array_push($where, 'AND (tbl_materials.name like "%' . $search . '%" OR tbl_materials.code like "%' . $search . '%")');
}

$group_by = "GROUP BY tblwarehouse_product.product_id,tblwarehouse_product.lot_code,date_sd,tblwarehouse_product.localtion";
$having = "HAVING SUM(tblwarehouse_product.quantity_left) > 0.0000000001";
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblwarehouse_product.product_id', 'tblwarehouse_product.id', 'type_export', 'tblwarehouse_product.date_warehouse','tblwarehouse_product.product_quantity_payment_left'], $group_by, [], [], $having);
$output  = $result['output'];
$rResult = $result['rResult'];

// usort($rResult, ch_make_cmp(['product_id' => "desc"]));
$currentPage = $this->_instance->input->post('start');
$currentall = $output['iTotalRecords'];
$footer_data = array(
    'slt' => 0,
    'gtt' => 0,
);
$j = 0;
$j = $this->_instance->input->post('start');

foreach ($rResult as $r => $aRow) {
    $row = [];
    // $get_items = get_items($aRow['product_id'], $aRow['tblwarehouse_product.type_items']);
    $j++;

    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'tblwarehouse_product.lot_code') {
            $_data = '<div class="text-center">' . $aRow['tblwarehouse_product.lot_code'] . '</div>';
        }
        

        if ($aColumns[$i] == '1') {
            $_data = $j;
        }
        if ($aColumns[$i] == 'tbl_materials.code') {
            $_data = '<div style="font-size:16px">' . $aRow['tbl_materials.code'] . '</div>';
        }
        if ($aColumns[$i] == 'tbl_materials.name') {
            $_data = '<div style="font-size:16px">' . $aRow['tbl_materials.name'] . '</div>';
        }
        if ($aColumns[$i] == 'tblunits.unit') {
            $_data = '<div class="text-center">' . ($aRow['tblunits.unit']) . '</div>';
        }
        if ($aColumns[$i] == 'tbl_species.name as species_name') {
            $_data = '<div class="text-left">' . ($aRow['species_name']) . '</div>';
        }
        if ($aColumns[$i] == 'tblwarehouse_product.date_sd as date_sd') {
            $_data = '<div class="text-center">' . _d($aRow['date_sd']) . '</div>';
        }
        if ($aColumns[$i] == 'SUM(tblwarehouse_product.product_quantity_payment_left*tblwarehouse_product.price) as price') {
            if($aRow['price'] == 0){
                $aRow['price'] = getpriceimport($aRow['product_id'], 'nvl') * $aRow['product_quantity_payment_left'];
            }
            $_data = '<div class="text-right">' . formatNumber($aRow['price']) . '</div>';
            $footer_data['gtt'] += $aRow['price'];
        }
        
        if ($aColumns[$i] == 'tblwarehouse_product.date_warehouse') {
            $date_warehouse = $aRow['tblwarehouse_product.date_warehouse'];
            // $check_export = get_table_where('tblwarehouse_export', array('product_id' => $aRow['product_id'], 'type_items' => 'nvl', 'lot_code' => $aRow['lot_code'], 'date_sd' => $aRow['date_sd']), '', 'row');
            $this->ci->db->where(['product_id' => $aRow['product_id'], 'type_items' => 'nvl', 'lot_code' => $aRow['lot_code'], 'date_sd' => $aRow['date_sd']]);
            $this->ci->db->limit(1);
            $this->ci->db->order_by('tblwarehouse_export.id desc');
            $check_export = $this->ci->db->get('tblwarehouse_export')->row();
            if (!empty($check_export)) {
                $date_warehouse = $check_export->date_warehouse;
            } else {
                if ($aRow['type_export'] == 1 || $aRow['type_export'] == 3 || $aRow['type_export'] == 20) {
                    // $_data = '<div class="text-center">' . _dhau($date_warehouse) . '</div>';
                } else {
                    if ($aRow['type_export'] == 2) {
                        if (!empty($aRow['import_id'])) {
                            $transfer =  get_table_where('tbltransfer_warehouse_detail', array('id_transfer' => $aRow['import_id'], 'id_items' => $aRow['product_id'], 'type' => 'nvl', 'lot_code' => $aRow['lot_code'], 'date_sd' => $aRow['date_sd']), '', 'row');
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
                        // $_data = '<div class="text-center">' . _dhau($date_warehouse) . '</div>';
                    } else {
                        // $_data = '<div class="text-center"></div>';
                    }
                }
            }

            $day_1 = explode(' ', $date_warehouse)[0];
            $day_2 = date('Y-m-d'); //current date
            $days = (strtotime($day_2) - strtotime($day_1)) / (60 * 60 * 24);
            $_data = '<div class="text-center">' . formatNumber($days) . '</div>';
        }
        if ($aColumns[$i] == '2') {
            $_data = '<div class="text-center">'._d($day_1).'</div>';
        }
        if ($aColumns[$i] == 'SUM(tblwarehouse_product.quantity_left) as quantity') {
            $_data = '<div class="text-center" style="font-size:17px;color:#3c763d;font-weight: bold">' . formatNumber($aRow['quantity']) . '</div>';
            $footer_data['slt'] += $aRow['quantity'];
        }

        $row[] = $_data;
    }

    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = formatNumber($total);
}
$output['sums'] = $footer_data;

$output['title_excel'] = ['Giai Đoạn Tại Thời Điểm: ' . _dt(date('Y-m-d H:i:s'))];
