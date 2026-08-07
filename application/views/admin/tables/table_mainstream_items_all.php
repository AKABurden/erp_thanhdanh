<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns     = array(
    'tblsuppliers.company',
    'tblmainstream_goods.id',
    'tblmainstream_goods.id_items',
);
$sIndexColumn = "id";
$sTable       = 'tblmainstream_goods';
$where        = array();
$join         = array(
    'LEFT JOIN tblsuppliers  ON tblsuppliers.id=tblmainstream_goods.id_suppliers',
);
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblsuppliers.id as id_suppliers', 'tblmainstream_goods.type',
), 'ORDER BY tblmainstream_goods.id_suppliers ASC');
$output       = $result['output'];
$rResult      = $result['rResult'];
$j = 0;
if (has_permission('suppliers', '', 'view_own') && !is_admin()) {
    array_push($where, 'AND  tblsuppliers.addedfrom = ' . get_staff_user_id());
}
foreach ($rResult as $key => $aRow) {

    if ($key == 0) {
        $id_suppliers = $aRow['id_suppliers'];
        $row = array(
            '#: ' . $aRow['tblsuppliers.company'],
        );
        $row['DT_RowClass'] = 'alert-header bold warning';

        for ($i = 0; $i < count($aColumns); $i++) {
            $row[] = "";
        }
        $output['aaData'][] = $row;
    } else {
        if ($aRow['id_suppliers'] != $id_suppliers) {
            $id_suppliers = $aRow['id_suppliers'];
            $row = array(
                '#: ' . $aRow['tblsuppliers.company'],
            );
            $row['DT_RowClass'] = 'alert-header bold warning';

            for ($i = 0; $i < count($aColumns); $i++) {
                $row[] = "";
            }
            $output['aaData'][] = $row;
        }
    }
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == 'tblsuppliers.company') {
            $_data = '';
        }
        if ($aRow['type'] == 'items') {
            $name = get_table_where('tblitems', array('id' => $aRow['tblmainstream_goods.id_items']), '', 'row');
        } else {
            $table = get_table_where('tbltype_items', array('type' => $aRow['type']), '', 'row');
            $name = get_table_where($table->table, array('id' => $aRow['tblmainstream_goods.id_items']), '', 'row');
        }

        if (!$name) {
            $name = new stdClass();
            $name->code = '';
            $name->name = '';
        }

        if ($aColumns[$i] == 'tblmainstream_goods.id') {
            $_data = $name->code .$aRow['tblmainstream_goods.id_items']. '<br>' . format_item_purchases($aRow['type']) . format_item_color($aRow['tblmainstream_goods.id_items'], $aRow['type']);
        }
        if ($aColumns[$i] == 'tblmainstream_goods.id_items') {
            $_data = $name->name;
        }
        $row[] = $_data;
    }

    $_data = '';
    if (is_admin()) {
        $_data = '<a href="#" class="btn btn-danger btn-icon " delete_combo ="' . $aRow['tblmainstream_goods.id'] . '"  onclick="delete_items(' . $aRow['tblmainstream_goods.id'] . '); return false;"><i class="fa fa-remove"></i></a>';
        $row[] = $_data;
    } else {
        $row[] = '';
    }
    $output['aaData'][] = $row;
}
