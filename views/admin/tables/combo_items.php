<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$aColumns     = array(
    'tblcombo_items.id',
    'tblitems.name',
    'tblcombo_items.quantity',
);
$sIndexColumn = "id";
$sTable       = 'tblcombo_items';
$where        = array(
   'AND rel_id="' . $id . '"'
);
$join         = array(
    'LEFT JOIN tblitems  ON tblitems.id=tblcombo_items.product_id'
);
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable,$join, $where, array(
    // 'tblroles.name',
));
$output       = $result['output'];
$rResult      = $result['rResult'];
// print_r($rResult);die();


$j=0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'tblcombo_items.id') {
            $_data = $j;
        }
        if ($aColumns[$i] == 'tblcombo_items.quantity') {
        if($type == 0)
        {
            $_data='<input class="form-control" onchange="update_quantity_combo('.$aRow['tblcombo_items.id'].',this)" value="'.$aRow['tblcombo_items.quantity'].'"/>';
        }else
        {
            $_data=$aRow['tblcombo_items.quantity'];
        }
        }
        $row[] = $_data;
    }
    $_data='';
    if (is_admin()) {
        // $_data = '<a href="#" class="btn btn-default btn-icon" onclick="view_init_department(' . $aRow['tblcombo_items.id'] . '); return false;"><i class="fa fa-pencil"></i></a>';
        $_data = '<a href="#" class="btn btn-danger btn-icon " delete_combo ="'.$aRow['tblcombo_items.id'].'"  onclick="delete_combo(' . $aRow['tblcombo_items.id'] . '); return false;"><i class="fa fa-remove"></i></a>';
        $row[]=$_data;
    } 
    else {
        $row[] = '';
    }
    $output['aaData'][] = $row;
}
