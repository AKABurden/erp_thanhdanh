<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$aColumns     = array(
    'tblmainstream_goods.id',
    'tblsuppliers.company',
    'tblmainstream_goods.price',
);
$sIndexColumn = "id";
$sTable       = 'tblmainstream_goods';
$where        = array(
   'AND id_items="' . $id . '"',
);
$join         = array(
    'LEFT JOIN tblsuppliers  on tblsuppliers.id = tblmainstream_goods.id_suppliers',
);
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable,$join, $where, array(
     'tblmainstream_goods.type',
));

$output       = $result['output'];
$rResult      = $result['rResult'];
$j=0;
foreach ($rResult as $aRow) {
       $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aColumns[$i] == 'tblmainstream_goods.id') {
            $_data='<div>'.$j.'</div>';
        }
        if ($aColumns[$i] == 'tblsuppliers.company') {
            $_data=$aRow['tblsuppliers.company'];
        }
        if ($aColumns[$i] == 'tblmainstream_goods.price') {
            $_data='<div class="text-right">'.number_format($aRow['tblmainstream_goods.price']).'</div>';
        }
        $row[] = $_data;
    }
        
    $_data='';
    if (is_admin()) {
        $_data = '<a href="#" class="btn btn-danger btn-icon "  onclick="delete_supplier(' . $aRow['tblmainstream_goods.id'] . '); return false;"><i class="fa fa-remove"></i></a>';
        $row[]=$_data;
    } 
    else {
        $row[] = '';
    }
    $output['aaData'][] = $row;
}
